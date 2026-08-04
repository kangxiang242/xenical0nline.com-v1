<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Release extends Model
{
    protected $fillable = [
        'version',
        'deployed_at',
        'token',
        'git_sha',
    ];

    protected $casts = [
        'deployed_at' => 'datetime',
    ];
}