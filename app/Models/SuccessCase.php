<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuccessCase extends Model
{
    protected $fillable = ['duration', 'result', 'before_image', 'after_image', 'content', 'name', 'age', 'occupation', 'sort', 'status'];
}
