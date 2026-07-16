<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compute extends Model
{
    protected $fillable = [
        'sex','age','height','weight','motion_level','bmi','bmr','tdee','ip','user_agent'
    ];

    const MOTION_LEVEL = [
        1=>'久坐/沒在運動',
        2=>'輕量運動，每星期運動 1 ~ 3 天',
        3=>'中量運動，每星期運動 3 ~ 5 天',
        4=>'高強運動，每星期運動 5 ~ 7 天',
        5=>'每天運動2次'
    ];
}
