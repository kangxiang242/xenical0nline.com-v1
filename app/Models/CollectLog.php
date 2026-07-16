<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectLog extends Model
{
    protected $fillable = [
        'content','is_error','data'
    ];
}
