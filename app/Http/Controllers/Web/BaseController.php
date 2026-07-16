<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;


class BaseController extends Controller
{
    protected function success($msg,$sub_msg='',$redirect=''){
        if(request()->ajax()){
            return json_encode(['type'=>'tips','msg'=>$msg,'sub_msg'=>$sub_msg,'code'=>200,'redirect'=>$redirect,'form_data'=>request()->except('_token','_method')],JSON_UNESCAPED_UNICODE);
        }else{
            session()->flash('flash',json_encode(['type'=>'tips','msg'=>$msg,'sub_msg'=>$sub_msg,'code'=>200,'form_data'=>request()->except('_token','_method')],JSON_UNESCAPED_UNICODE));

            if($redirect){
                return redirect($redirect);
            }
        }
    }

    protected function error($msg,$sub_msg='',$redirect=''){
        if(request()->ajax()){
            return json_encode(['type'=>'tips','msg'=>$msg,'sub_msg'=>$sub_msg,'code'=>400,'redirect'=>$redirect,'form_data'=>request()->except('_token','_method')],JSON_UNESCAPED_UNICODE);
        }else{
            session()->flash('flash',json_encode(['type'=>'tips','sub_msg'=>$sub_msg,'msg'=>$msg,'code'=>400,'form_data'=>request()->except('_token','_method')],JSON_UNESCAPED_UNICODE));

            if($redirect){

                return redirect($redirect);
            }
        }
    }
}
