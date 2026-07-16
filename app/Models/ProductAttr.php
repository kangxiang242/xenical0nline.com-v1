<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttr extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'product_id','name','value','status'
    ];
}
