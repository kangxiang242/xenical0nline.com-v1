<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'cate_id','phone','star','content','ip','user_agent','status','is_admin','up','order_no'
    ];

    protected $dates = [
        'time'
    ];

    public function cate()
    {
        return $this->belongsTo(Cate::class);
    }
}
