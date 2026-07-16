<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ConfigService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BuyerMessageController extends Controller
{
    const CACHE_KEY_BOX_BUYERS = 'buyer_message:box_buyers';
    const CACHE_KEY_PERCENTAGES = 'buyer_message:percentages';
    const CACHE_KEY_TIMESTAMP = 'buyer_message:timestamp';
    private $cacheExpiration = 86400;

    private $config = [
        'totalBuyers' => 200,
        'boxPercentages' => [
            1 => 12,
            3 => 18,
            6 => 20,
            9 => 18,
            12 => 17,
            15 => 8,
            18 => 7,
        ],
        'timeSlots' => [
            'default' => ['initialDelay' => 3000, 'intervalMin' => 4000, 'intervalMax' => 8000],
        ],
        'stayDuration' => 3000,
    ];

    /**
     * 获取各盒数的购买人数
     */
    public function boxBuyers()
    {
        $boxBuyers = $this->getBoxBuyers();
        return response()->json([
            'code' => 1,
            'msg' => 'success',
            'data' => $boxBuyers,
        ]);
    }

    /**
     * 获取下一条购买消息
     */
    public function nextMessage()
    {
        $result = $this->getNextMessage();
        return response()->json([
            'code' => 1,
            'msg' => 'success',
            'data' => $result,
        ]);
    }

    /**
     * 增加购买人数
     */
    public function incrementBuyer(Request $request)
    {
        $boxNum = $request->input('boxNum', 1);
        $boxBuyers = $this->getBoxBuyers();

        if (!isset($boxBuyers[$boxNum])) {
            $boxBuyers[$boxNum] = 0;
        }

        $boxBuyers[$boxNum]++;
        Cache::put(self::CACHE_KEY_BOX_BUYERS, $boxBuyers, $this->cacheExpiration);

        return response()->json([
            'code' => 1,
            'msg' => 'success',
            'data' => [
                'boxNum' => $boxNum,
                'count' => $boxBuyers[$boxNum],
            ],
        ]);
    }

    /**
     * 增加首页用户计数
     */
    public function incrementUserCount()
    {
        $currentCount = (int)ConfigService::get('index_user_count', '124344649');
        $newCount = $currentCount + 1;
        
        // 更新数据库
        $config = \App\Models\Config::where('name', 'index_user_count')->first();
        if ($config) {
            $config->content = (string)$newCount;
            $config->save();
        } else {
            // 配置不存在时创建
            \App\Models\Config::create([
                'name' => 'index_user_count',
                'type' => 'text',
                'content' => (string)$newCount,
            ]);
        }
        Cache::forget('config:index_user_count');

        return response()->json([
            'code' => 1,
            'msg' => 'success',
            'data' => [
                'count' => $newCount,
            ],
        ]);
    }

    /**
     * 初始化或获取购买人数数据
     */
    private function getBoxBuyers()
    {
        $buyers = Cache::get(self::CACHE_KEY_BOX_BUYERS);
        $timestamp = Cache::get(self::CACHE_KEY_TIMESTAMP);

        if (!$buyers || !$timestamp || (time() - $timestamp > $this->cacheExpiration)) {
            $percentages = $this->selectPercentagesFromRanges();
            $buyers = $this->calculateBuyers($percentages);
            $this->saveToCache($buyers, $percentages, time());
        }

        $allBoxes = array_keys($this->config['boxPercentages']);
        foreach ($allBoxes as $boxNum) {
            if (!isset($buyers[$boxNum])) {
                $buyers[$boxNum] = rand(80, 200);
            }
        }

        return $buyers ?: [];
    }

    private function selectPercentagesFromRanges()
    {
        $ranges = $this->config['boxPercentages'];
        $selectedPercentages = [];
        $total = 0;

        foreach ($ranges as $boxNum => $range) {
            if (is_numeric($range)) {
                $selectedPercentages[$boxNum] = $range;
                $total += $range;
            }
        }

        return $selectedPercentages;
    }

    private function calculateBuyers($percentages)
    {
        $boxBuyers = [];
        $allBoxes = array_keys($this->config['boxPercentages']);

        foreach ($allBoxes as $boxNum) {
            $cacheKey = self::CACHE_KEY_BOX_BUYERS . ':' . $boxNum;
            $count = Cache::get($cacheKey);

            if (!$count || $count < 80 || $count > 200) {
                $count = rand(80, 200);
                Cache::put($cacheKey, $count, $this->cacheExpiration);
            }

            $boxBuyers[$boxNum] = $count;
        }

        return $boxBuyers;
    }

    private function saveToCache($buyers, $percentages, $timestamp)
    {
        Cache::put(self::CACHE_KEY_BOX_BUYERS, $buyers, $this->cacheExpiration);
        Cache::put(self::CACHE_KEY_PERCENTAGES, $percentages, $this->cacheExpiration);
        Cache::put(self::CACHE_KEY_TIMESTAMP, $timestamp, $this->cacheExpiration);
    }

    private function buildWeightedPool($percentages)
    {
        $pool = [];
        foreach ($percentages as $boxNum => $percentage) {
            for ($i = 0; $i < $percentage; $i++) {
                $pool[] = $boxNum;
            }
        }
        return $pool;
    }

    private function getNextMessage()
    {
        $boxBuyers = $this->getBoxBuyers();
        $percentages = Cache::get(self::CACHE_KEY_PERCENTAGES) ?: $this->selectPercentagesFromRanges();

        $pool = $this->buildWeightedPool($percentages);
        $boxNum = !empty($pool) ? $pool[array_rand($pool)] : 1;

        $phone = rand(100, 999);

        if (!isset($boxBuyers[$boxNum])) {
            $boxBuyers[$boxNum] = 0;
        }
        $boxBuyers[$boxNum]++;

        Cache::put(self::CACHE_KEY_BOX_BUYERS, $boxBuyers, $this->cacheExpiration);

        $nextInterval = rand(4000, 8000);

        $messageHtml = '<p>09** *** <span class="update-phone">' . $phone . '</span> 剛完成訂購 <span class="update-num">' . $boxNum . '</span>盒</p>';

        // 同时增加首页用户计数
        $this->incrementUserCountInternal();

        return [
            'shouldShow' => true,
            'messageHtml' => $messageHtml,
            'boxNum' => $boxNum,
            'nextInterval' => $nextInterval,
            'boxBuyers' => $boxBuyers,
        ];
    }

    private function incrementUserCountInternal()
    {
        $currentCount = (int)ConfigService::get('index_user_count', '124344649');
        $newCount = $currentCount + 1;
        
        $config = \App\Models\Config::where('name', 'index_user_count')->first();
        if ($config) {
            $config->content = (string)$newCount;
            $config->save();
        } else {
            // 配置不存在时创建
            \App\Models\Config::create([
                'name' => 'index_user_count',
                'type' => 'text',
                'content' => (string)$newCount,
            ]);
        }
        Cache::forget('config:index_user_count');
    }

    private function getCurrentTimeSlotConfig()
    {
        return [
            'initialDelay' => 3000,
            'intervalMin' => 4000,
            'intervalMax' => 8000,
        ];
    }
}
