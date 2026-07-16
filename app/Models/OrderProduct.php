<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    protected $casts = [
        'product' => 'json', // 声明json类型
    ];
}
