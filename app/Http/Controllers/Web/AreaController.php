<?php

namespace App\Http\Controllers\Web;

use App\Handlers\DeviceTypeHandlers;
use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Services\VehicleService;
use Illuminate\Http\Request;
use Rizhou\Control\Supply\StoreSynchronizing;

class AreaController extends Controller
{
    private $request_city_name;
    private $request_county_name;
    private $request_road_name;

    public function __construct(Request $request)
    {
        $this->request_city_name = $request->city_name?trim($request->city_name):"";

        $this->request_county_name = $request->county_name?trim($request->county_name):"";

        $this->request_road_name = $request->road_name?trim($request->road_name):"";
    }

    public function get(Request $request){
        $area = Area::where('parent_id',$request->get('pid',0))->where('is_special',0)->get()->toJson();
        return response()->json($area);
    }

    public function getCity(Request $request){
        if($request->type == 1){
            $data = StoreSynchronizing::make()->getCity();

        }elseif($request->type == 2){

            $delivery_type_all = \App\Services\ConfigService::get('delivery_type',[]);
            if($delivery_type_all){
                $delivery_type_all = json_decode(\App\Services\ConfigService::get('delivery_type',[]),true);
            }
            if(in_array(4,$delivery_type_all)){
                $data = StoreSynchronizing::make('ezship')->getCity();
            }elseif (in_array(3,$delivery_type_all)){
                $data = StoreSynchronizing::make('ezship-family')->getCity();
            }else{
                return response()->json([]);
            }

        }else{
            $data = Area::where('parent_id',0)->where('is_special',0)->select(['id','parent_id as pid','level','name'])->get()->toJson();
        }
        return response()->json($data);
    }

    public function getCounty(Request $request){
        if($request->type == 1){
            $data = StoreSynchronizing::make()->getCounty($this->request_city_name);
        }elseif($request->type == 2){

            $delivery_type_all = \App\Services\ConfigService::get('delivery_type',[]);
            if($delivery_type_all){
                $delivery_type_all = json_decode(\App\Services\ConfigService::get('delivery_type',[]),true);
            }
            if(in_array(4,$delivery_type_all)){
                $data = StoreSynchronizing::make('ezship')->getCounty($this->request_city_name);
            }elseif (in_array(3,$delivery_type_all)){
                $data = StoreSynchronizing::make('ezship-family')->getCounty($this->request_city_name);
            }else{
                return response()->json([]);
            }

        }else{
            $city = Area::where('parent_id',0)->where('is_special',0)->where('name',$this->request_city_name)->select(['id','parent_id as pid','level','name'])->first();
            $data = Area::where('parent_id',$city->id)->where('is_special',0)->select(['id','parent_id as pid','level','name'])->get()->toJson();
        }
        return response()->json($data);
    }

    public function getRoad(Request $request){
        if($request->type == 1){
            $data = StoreSynchronizing::make()->getRoad($this->request_city_name,$this->request_county_name);
        }elseif($request->type == 2){

            $delivery_type_all = \App\Services\ConfigService::get('delivery_type',[]);
            if($delivery_type_all){
                $delivery_type_all = json_decode(\App\Services\ConfigService::get('delivery_type',[]),true);
            }
            if(in_array(4,$delivery_type_all)){
                $data = StoreSynchronizing::make('ezship')->getRoad($this->request_city_name,$this->request_county_name);
            }elseif (in_array(3,$delivery_type_all)){
                $data = StoreSynchronizing::make('ezship-family')->getRoad($this->request_city_name,$this->request_county_name);
            }else{
                return response()->json([]);
            }

        }else{
            $city = Area::where('parent_id',0)->where('is_special',0)->where('name',$this->request_city_name)->select(['id','parent_id as pid','level','name'])->first();
            $county = Area::where('parent_id',$city->id)->where('is_special',0)->where('name',$this->request_county_name)->select(['id','parent_id as pid','level','name'])->first();
            $data = Area::where('parent_id',$county->id)->where('is_special',0)->select(['id','parent_id as pid','level','name'])->get()->toJson();
        }
        return response()->json($data);
    }

    public function getShop(Request $request){
        $data = [];
        if($request->type == 1){
            $data = StoreSynchronizing::make()->getShop($this->request_city_name,$this->request_county_name,$this->request_road_name);
        }elseif($request->type == 2){

            $delivery_type_all = \App\Services\ConfigService::get('delivery_type',[]);
            if($delivery_type_all){
                $delivery_type_all = json_decode(\App\Services\ConfigService::get('delivery_type',[]),true);
            }
            if(in_array(4,$delivery_type_all)){
                $data = StoreSynchronizing::make('ezship')->getShop($this->request_city_name,$this->request_county_name,$this->request_road_name);
            }elseif (in_array(3,$delivery_type_all)){
                $data = StoreSynchronizing::make('ezship-family')->getShop($this->request_city_name,$this->request_county_name,$this->request_road_name);
            }else{
                return response("");
            }

        }

        $city_name = $this->request_city_name;
        $county_name = $this->request_county_name;
        /*if (DeviceTypeHandlers::isMobile()){
            return view('mobile.widgets.shopping-store-item',compact('data','city_name','county_name'))->render();
        }else{

        }*/
        return view('web.widgets.shopping-store-item',compact('data','city_name','county_name'))->render();

    }


}
