<?php


namespace App\Services;


class VehicleService
{
    public static function IP(){
        return request()->header('cf-connecting-ip',request()->ip());
    }

    public static function userAgent(){
        return request()->header('user-agent');
    }

}
