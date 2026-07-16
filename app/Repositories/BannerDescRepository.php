<?php

namespace App\Repositories;

use App\Models\BannerDesc;

class BannerDescRepository extends Repository
{
    protected $modelClass = BannerDesc::class;

    public function all()
    {
        return $this->remember(function () {
            return $this->model()->get();
        });
    }

    public function first()
    {
        return $this->remember(function () {
            return $this->model()->first();
        });
    }
}
