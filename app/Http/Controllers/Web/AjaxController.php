<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Theme;
use App\View\Pages\LGBT;
use Illuminate\Http\Request;

class AjaxController extends Controller
{
    public function switchLgbt(Request $request){
        if($request->cate_id && $request->id){
            $theme = Theme::where('cate_id',$request->cate_id)->first();
            if($theme){
                return LGBT::render($theme,$request->id);
            }

        }
        return "";

    }
}
