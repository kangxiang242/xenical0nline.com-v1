<?php


namespace App\Handlers;


class DeviceTypeHandlers
{

    public static function isMobile($user_agent = null) {
        if(!$user_agent){
            $user_agent  = $_SERVER['HTTP_USER_AGENT'] ?? '';
        }
        $mobile_browser = Array (
            "iphone",
            "android",
            "ios",
            "ipad",
        );
        $is_mobile = false;
        foreach ( $mobile_browser as $device ) {
            if (stristr ( $user_agent, $device )) {
                $is_mobile = true;
                break;
            }
        }
        return $is_mobile;
    }


    public static function getDevice($agent = null){
        if(!$agent){
            $agent  = $_SERVER['HTTP_USER_AGENT'] ?? '' ?? '';
        }
        $agent = strtolower($agent);

        $device_type = 'unknown';

        $device_type = (strpos($agent, 'windows')) ? 'windows' : $device_type;

        $device_type = (strpos($agent, 'mac')) ? 'mac' : $device_type;

        $device_type = (strpos($agent, 'iphone')) ? 'iphone' : $device_type;

        $device_type = (strpos($agent, 'ipad')) ? 'ipad' : $device_type;

        $device_type = (strpos($agent, 'linux')) ? 'linux' : $device_type;

        $device_type = (strpos($agent, 'android')) ? 'android' : $device_type;

        return $device_type;

    }

    public static function getBrowser($agent = null): string
    {
        if (!$agent) {
            $agent = $_SERVER['HTTP_USER_AGENT'] ?? '' ?? '';
        }
        $agent = strtolower($agent);

        $browsers = [
            'edg' => 'Edge',
            'edge' => 'Edge',
            'opr' => 'Opera',
            'opera' => 'Opera',
            'chrome' => 'Chrome',
            'safari' => 'Safari',
            'firefox' => 'Firefox',
        ];

        foreach ($browsers as $key => $name) {
            if (strpos($agent, $key) !== false) {
                return $name;
            }
        }

        return 'unknown';
    }

    public static function getCrawler($agent = null) {
        if($agent){
            $agent = strtolower($agent);
        }else{
            $agent= strtolower($_SERVER['HTTP_USER_AGENT'] ?? '' ?? '');
        }
        if (!empty($agent)) {
            $spiderSite= array(
                "TencentTraveler",
                "Baiduspider+",
                "BaiduGame",
                "Googlebot",
                "msnbot",
                "Sosospider+",
                "Sogou web spider",
                "ia_archiver",
                "Yahoo! Slurp",
                "YoudaoBot",
                "Yahoo Slurp",
                "MSNBot",
                "Java (Often spam bot)",
                "BaiDuSpider",
                "Voila",
                "Yandex bot",
                "BSpider",
                "twiceler",
                "Sogou Spider",
                "Speedy Spider",
                "Google AdSense",
                "Heritrix",
                "Python-urllib",
                "Alexa (IA Archiver)",
                "Ask",
                "Exabot",
                "Custo",
                "OutfoxBot/YodaoBot",
                "yacy",
                "SurveyBot",
                "legs",
                "lwp-trivial",
                "Nutch",
                "StackRambler",
                "The web archive (IA Archiver)",
                "Perl tool",
                "MJ12bot",
                "Netcraft",
                "MSIECrawler",
                "WGet tools",
                "larbin",
                "Fish search",
            );
            foreach($spiderSite as $val) {
                $str = strtolower($val);
                if (strpos($agent, $str) !== false) {
                    return $str;
                }
            }
            return null;
        } else {
            return null;
        }
    }
}
