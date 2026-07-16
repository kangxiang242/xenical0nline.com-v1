<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class ProductCate extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'product_id','cate_id','is_main'
    ];

    public function product()
    {
        return $this->hasOne(Product::class,'id','product_id')->orderBy('sort','desc');
    }

    public function cate()
    {
        return $this->hasOne(Cate::class,'id','cate_id');
    }
}
