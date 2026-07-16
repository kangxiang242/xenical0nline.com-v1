<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Dcat\Admin\Traits\ModelTree;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\EloquentSortable\Sortable;
class Cate extends Model implements Sortable
{
    use ModelTree{
        allNodes as treeAllNodes;
        ModelTree::boot as treeBoot;
    }

    protected $fillable = [
        'pid','name','status','sort'
    ];

    protected $titleColumn = 'name';

    protected $orderColumn = 'sort';


    protected $parentColumn = 'pid';

    /**
     * 获取下级id
     */
    public function sub()
    {
        return $this->hasMany(Cate::class, 'pid', 'id')->orderBy('sort','asc');
    }

    /**
     * 获取上级
     */
    public function parent()
    {
        return $this->hasOne(Cate::class, 'id', 'pid');
    }

    /**
     * 定义你的关联模型.
     *
     * @return BelongsToMany
     */
    public function product(): BelongsToMany
    {
        $pivotTable = 'product_cates'; // 中间表

        $relatedModel = Product::class;

        return $this->belongsToMany($relatedModel, $pivotTable, 'product_id', 'cate_id');
    }


}
