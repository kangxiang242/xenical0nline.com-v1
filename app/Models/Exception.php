<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exception extends Model
{
    protected $fillable = [
        'ip','ip_country','status_code','message','uri','method','user_agent','parameters','headers','trace','referer'
    ];

    protected $casts = [
        'parameters'=>'json','headers'=>'json','trace'=>'json'
    ];

}
