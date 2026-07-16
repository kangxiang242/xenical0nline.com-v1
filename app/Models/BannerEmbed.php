<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BannerEmbed extends Model
{
    protected $fillable = [
        'banner_id','type','content','x','y','style','img_path','img_alt','img_size','debug'
    ];
}
