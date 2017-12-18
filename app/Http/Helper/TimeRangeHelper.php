<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/9/28
 * Time: 下午 4:07
 */

namespace App\Http\Helper;


class TimeRangeHelper
{
    public static function hour()
    {
        $currentTime = date('Y-m-d H');
        return $currentTime;
    }
}