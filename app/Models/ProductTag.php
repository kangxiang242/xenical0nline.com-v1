<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductTag extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'product_id','tag_id'
    ];

    public function product()
    {
        return $this->hasOne(Product::class,'id','product_id');
    }

}
