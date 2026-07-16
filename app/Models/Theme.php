<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    protected $fillable = [
        'cate_id','data','type','title','banner','css','device'
    ];

    protected $casts = [
        'data' => 'json', // 声明json类型
    ];
}
