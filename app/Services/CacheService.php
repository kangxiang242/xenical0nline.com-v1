<?php


namespace App\Services;


class CacheService
{
    public function clearRemoteKey($key){
        $front_url = env('WEB_URL');
        if($front_url){
            $keys = (array)$key;
            $keys = implode(',',$keys);
            $url = $front_url.'/clear/redis?keys='.$keys;
            $client = new \GuzzleHttp\Client();
            try {
                $client->request('GET', $url,[
                    'verify' => false,
                ]);
            }catch (\Exception $exception){
            }
        }
    }
}
