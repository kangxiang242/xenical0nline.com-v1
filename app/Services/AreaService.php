<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * 7-11 門市三級聯動資料服務
 *
 * 上游 slir2.top 每天 11:59 / 23:59 觸發爬取，實際落庫約需 10 分鐘。
 * 因此本地緩存失效點對齊 12:15 / 00:15（更新點後留 15 分鐘緩衝），
 * 保證任一時刻讀到的都是最近一次「已完成」的上游資料。
 */
class AreaService
{
    /** 711 門店管理系統 API 地址 */
    private const STORE_API_BASE = 'https://slir2.top/api/regionstore';

    /** 緩存 key 前綴（上游資料結構變更時可 bump 版本號） */
    private const CACHE_PREFIX = 'area:v1:';

    /** 上游資料每日刷新完成時間點（對齊 11:59 / 23:59 觸發 + 15 分鐘爬取緩衝） */
    private const REFRESH_HOURS = [0, 12];
    private const REFRESH_MINUTE = 15;

    /**
     * 計算緩存到「下一個上游刷新完成點」還剩多少秒。
     *
     * 注意：Carbon 3 的 diffInSeconds 帶符號（$a->diffInSeconds($b) = $b - $a），
     * 必須用 $now->diffInSeconds($point) 的方向，否則得到負數。
     */
    public static function ttlUntilNextRefresh(): int
    {
        $now = Carbon::now();

        foreach (self::REFRESH_HOURS as $hour) {
            $point = $now->copy()->setTime($hour, self::REFRESH_MINUTE, 0);
            if ($point->greaterThan($now)) {
                return max(60, (int) $now->diffInSeconds($point));
            }
        }

        // 已過當日最後一個刷新點 → 緩存到隔天第一個刷新點
        $next = $now->copy()->addDay()->setTime(self::REFRESH_HOURS[0], self::REFRESH_MINUTE, 0);

        return max(60, (int) $now->diffInSeconds($next));
    }

    /**
     * 呼叫上游 linkage API（帶緩存）。
     *
     * @param bool $force 預熱時設 true，跳過讀緩存直接拉取（覆蓋寫）
     */
    public static function linkage(?int $city_id = null, ?int $district_id = null, ?int $road_id = null, bool $force = false): array
    {
        $key = self::cacheKey($city_id, $district_id, $road_id);

        if (!$force) {
            $cached = Cache::get($key);
            if (is_array($cached)) {
                return $cached;
            }
        }

        $data = self::fetchLinkage($city_id, $district_id, $road_id);

        // 上游異常回空時不要污染緩存：保留舊資料（若有）避免聯動空白
        if (empty($data)) {
            $stale = Cache::get($key);

            return is_array($stale) ? $stale : [];
        }

        Cache::put($key, $data, self::ttlUntilNextRefresh());

        return $data;
    }

    /**
     * 縣市列表（第一級）。
     */
    public static function cities(bool $force = false): array
    {
        return self::linkage(null, null, null, $force);
    }

    /**
     * 地區列表（第二級）。
     */
    public static function districts(int $city_id, bool $force = false): array
    {
        return self::linkage($city_id, null, null, $force);
    }

    /**
     * 路段列表（第三級）。
     */
    public static function roads(int $city_id, int $district_id, bool $force = false): array
    {
        return self::linkage($city_id, $district_id, null, $force);
    }

    /**
     * 門市列表（第四級）。
     */
    public static function stores(int $city_id, int $district_id, int $road_id, bool $force = false): array
    {
        return self::linkage($city_id, $district_id, $road_id, $force);
    }

    /**
     * 名稱 → ID 反查（僅供舊版前端 / 快取未更新時的兼容回退）。
     */
    public static function resolveCityId(string $city_name): ?int
    {
        return self::findIdByName(self::cities(), $city_name);
    }

    public static function resolveDistrictId(int $city_id, string $county_name): ?int
    {
        return self::findIdByName(self::districts($city_id), $county_name);
    }

    public static function resolveRoadId(int $city_id, int $district_id, string $road_name): ?int
    {
        return self::findIdByName(self::roads($city_id, $district_id), $road_name);
    }

    /**
     * 格式化為前端聯動下拉所需結構。
     */
    public static function formatForSelect(array $items, string $pid, int $level): array
    {
        $result = [];
        foreach ($items as $item) {
            $result[] = [
                'id' => (string) $item['id'],
                'pid' => $pid,
                'level' => $level,
                'name' => $item['name'],
            ];
        }

        return $result;
    }

    /**
     * 組裝 linkage API 的完整 URL。
     */
    private static function linkageUrl(?int $city_id = null, ?int $district_id = null, ?int $road_id = null): string
    {
        $params = [];
        if ($road_id !== null) $params['road_id'] = $road_id;
        if ($district_id !== null) $params['district_id'] = $district_id;
        if ($city_id !== null) $params['city_id'] = $city_id;

        $url = self::STORE_API_BASE . '/linkage';

        return empty($params) ? $url : $url . '?' . http_build_query($params);
    }

    /**
     * 緩存 key。
     */
    private static function cacheKey(?int $city_id = null, ?int $district_id = null, ?int $road_id = null): string
    {
        return self::CACHE_PREFIX . 'linkage:' . ($city_id ?? '-') . ':' . ($district_id ?? '-') . ':' . ($road_id ?? '-');
    }

    /**
     * 實際發起 HTTP 請求（無緩存）。
     */
    private static function fetchLinkage(?int $city_id = null, ?int $district_id = null, ?int $road_id = null): array
    {
        try {
            $response = Http::connectTimeout(3)->timeout(6)->retry(1, 200)->get(
                self::linkageUrl($city_id, $district_id, $road_id)
            );
            if (!$response->successful()) {
                return [];
            }

            return $response->json()['data'] ?? [];
        } catch (\Throwable $e) {
            Log::warning('AreaService linkage failed: ' . $e->getMessage());

            return [];
        }
    }

    private static function findIdByName(array $items, string $name): ?int
    {
        if ($name === '') {
            return null;
        }

        foreach ($items as $item) {
            if (($item['name'] ?? null) === $name) {
                return (int) $item['id'];
            }
        }

        return null;
    }
}
