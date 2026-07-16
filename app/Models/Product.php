<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class Product extends Model
{
    protected $fillable = [
        'name','brand_id','code','img','m_img','subtitle','price','market_price','discount_percent','status','is_stock','sort','tags','describe','collect_code','added'
    ];

    protected $casts = [
        'added'=>'json',
    ];

    /**
     * 定义你的关联模型.
     *
     * @return BelongsToMany
     */
    public function cate(): BelongsToMany
    {
        $pivotTable = 'product_cates'; // 中间表

        $relatedModel = Cate::class;

        return $this->belongsToMany($relatedModel, $pivotTable, 'product_id', 'cate_id');
    }

    public function productCate(){
        return $this->hasMany(ProductCate::class,'product_id','id');
    }

    public function tags()
    {

        return $this->belongsToMany(Tag::class,'product_tags','product_id','tag_id');
    }

    public function attr(){
        return $this->hasMany(ProductAttr::class,'product_id','id');
    }

    public function addeds()
    {
        return $this->belongsToMany(Product::class,'product_addeds','product_id','added_product_id');
    }

    public function faqs()
    {
        return $this->hasMany(ProductFaq::class)->orderBy('sort');
    }

}
