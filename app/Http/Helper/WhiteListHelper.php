<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/11/6
 * Time: 下午 3:40
 */

namespace App\Http\Helper;


use Illuminate\Support\Facades\Redis;

class WhiteListHelper
{
    private static $white_ip = [];

    public static function getWhiteIp()
    {
        return self::checkWhiteIp();
    }

    public static function checkWhiteIp()
    {
        try {
            $redis = Redis::connection('whiteList');

            if ($redis->get('whiteListStatus') == 1) {
                $ip_list = $redis->hgetall('ip');
                foreach ($ip_list as $value) {
                    $ip[] = $value;
                }
                self::$white_ip = $ip;
            } else {
                return [];
            }
            return self::$white_ip;
        } catch (\Exception $e) {
            return [];
        }
    }

}