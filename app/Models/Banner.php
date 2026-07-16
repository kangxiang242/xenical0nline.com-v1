<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    public function embeds()
    {
        return $this->hasMany(BannerEmbed::class);
    }
}
