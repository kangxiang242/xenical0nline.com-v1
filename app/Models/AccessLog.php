<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AccessLog
 * @package App\Models
 */
class AccessLog extends Model
{
    protected $table = 'jou_access_logs';

    protected $fillable = [
        'url','method','host','referer','ip','user_agent','device','crawler'
    ];
}
