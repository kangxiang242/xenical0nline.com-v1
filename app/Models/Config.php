<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Config extends Model
{

    protected $fillable = [
        'name','type','content'
    ];

    protected static function booted()
    {
        // 保存后清除缓存
        static::saved(function ($config) {
            self::clearCache($config->name);
        });

        static::deleted(function ($config) {
            self::clearCache($config->name);
        });
    }

    protected static function clearCache($key)
    {
        Cache::forget('config:' . $key);
    }
}
