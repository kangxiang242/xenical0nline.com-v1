<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Services\AreaService;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    private $request_city_name;
    private $request_county_name;
    private $request_road_name;

    public function __construct(Request $request)
    {
        $this->request_city_name = $request->city_name ? trim($request->city_name) : "";
        $this->request_county_name = $request->county_name ? trim($request->county_name) : "";
        $this->request_road_name = $request->road_name ? trim($request->road_name) : "";
    }

    public function get(Request $request)
    {
        $area = Area::where('parent_id', $request->get('pid', 0))->where('is_special', 0)->get()->toJson();
        return response()->json($area);
    }

    /**
     * 取得 city_id：優先用前端直傳的 ID，回退到名稱反查（兼容舊快取的 JS）。
     */
    private function resolveCity(Request $request): ?int
    {
        $city_id = (int) $request->input('city_id', 0);
        if ($city_id > 0) {
            return $city_id;
        }

        return $this->request_city_name !== '' ? AreaService::resolveCityId($this->request_city_name) : null;
    }

    /**
     * 取得 district_id：優先用前端直傳的 ID，回退到名稱反查。
     */
    private function resolveDistrict(Request $request, int $city_id): ?int
    {
        $district_id = (int) $request->input('district_id', 0);
        if ($district_id > 0) {
            return $district_id;
        }

        return $this->request_county_name !== '' ? AreaService::resolveDistrictId($city_id, $this->request_county_name) : null;
    }

    /**
     * 取得 road_id：優先用前端直傳的 ID，回退到名稱反查。
     */
    private function resolveRoad(Request $request, int $city_id, int $district_id): ?int
    {
        $road_id = (int) $request->input('road_id', 0);
        if ($road_id > 0) {
            return $road_id;
        }

        return $this->request_road_name !== '' ? AreaService::resolveRoadId($city_id, $district_id, $this->request_road_name) : null;
    }

    public function getCity(Request $request)
    {
        // type=1（7-11便利店）和 type=0（宅配/黑貓）都用 slir2.top 的 linkage API
        // 資料已由 AreaService 緩存（對齊上游 00:15 / 12:15 刷新點）
        if ($request->type == 1 || $request->type == 0) {
            return response()->json(AreaService::formatForSelect(AreaService::cities(), '0', 1));
        }

        // 後備：從本地 areas 表載入
        $data = Area::where('parent_id', 0)->where('is_special', 0)->select(['id', 'parent_id as pid', 'level', 'name'])->get()->toJson();
        return response()->json($data);
    }

    public function getCounty(Request $request)
    {
        if ($request->type == 1 || $request->type == 0) {
            $city_id = $this->resolveCity($request);
            if (!$city_id) {
                return response()->json([]);
            }

            return response()->json(
                AreaService::formatForSelect(AreaService::districts($city_id), (string) $city_id, 2)
            );
        }

        // 後備：從本地 areas 表載入
        $city = Area::where('parent_id', 0)->where('is_special', 0)->where('name', $this->request_city_name)->select(['id', 'parent_id as pid', 'level', 'name'])->first();
        if (!$city) {
            return response()->json([]);
        }
        $data = Area::where('parent_id', $city->id)->where('is_special', 0)->select(['id', 'parent_id as pid', 'level', 'name'])->get()->toJson();
        return response()->json($data);
    }

    public function getRoad(Request $request)
    {
        if ($request->type == 1 || $request->type == 0) {
            $city_id = $this->resolveCity($request);
            if (!$city_id) {
                return response()->json([]);
            }

            $district_id = $this->resolveDistrict($request, $city_id);
            if (!$district_id) {
                return response()->json([]);
            }

            return response()->json(
                AreaService::formatForSelect(AreaService::roads($city_id, $district_id), (string) $district_id, 3)
            );
        }

        // 後備：從本地 areas 表載入
        $city = Area::where('parent_id', 0)->where('is_special', 0)->where('name', $this->request_city_name)->select(['id', 'parent_id as pid', 'level', 'name'])->first();
        if (!$city) {
            return response()->json([]);
        }
        $county = Area::where('parent_id', $city->id)->where('is_special', 0)->where('name', $this->request_county_name)->select(['id', 'parent_id as pid', 'level', 'name'])->first();
        if (!$county) {
            return response()->json([]);
        }
        $data = Area::where('parent_id', $county->id)->where('is_special', 0)->select(['id', 'parent_id as pid', 'level', 'name'])->get()->toJson();
        return response()->json($data);
    }

    public function getShop(Request $request)
    {
        $data = [];
        $city_name = $this->request_city_name;
        $county_name = $this->request_county_name;

        if ($request->type == 1) {
            $city_id = $this->resolveCity($request);
            $district_id = $city_id ? $this->resolveDistrict($request, $city_id) : null;
            $road_id = ($city_id && $district_id) ? $this->resolveRoad($request, $city_id, $district_id) : null;

            if ($city_id && $district_id && $road_id) {
                // Map API field names to blade template field names
                foreach (AreaService::stores($city_id, $district_id, $road_id) as $item) {
                    $data[] = [
                        'shop_no' => $item['store_no'] ?? '',
                        'shop_name' => $item['store_name'] ?? '',
                        'shop_address' => $item['address'] ?? '',
                    ];
                }
            }
        }

        if (\App\Handlers\DeviceTypeHandlers::isMobile()) {
            return view('mobile.widgets.shopping-store-item', compact('data', 'city_name', 'county_name'))->render();
        }

        return view('web.widgets.shopping-store-item', compact('data', 'city_name', 'county_name'))->render();
    }

    public function get711(Request $request)
    {
        return response()->json([]);
    }
}
