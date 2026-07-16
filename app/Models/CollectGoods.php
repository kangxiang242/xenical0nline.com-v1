<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectGoods extends Model
{
    protected $fillable = [
        'goods_code','is_stock'
    ];
}
