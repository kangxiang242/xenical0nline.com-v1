<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Message extends Model
{
    protected $fillable = [
        'name','phone','email','content','ip','user_agent','type'
    ];


}
