<?php

namespace App\Http\Helper;

use Illuminate\Support\Facades\Cache;

class CacheHelper
{
    //Session 是否存在,不存在時將callback的return value 存入
    public function doesItExist($key, callable $callback)
    {
        $value = Cache::get($key);
        if ($value) {
            return $value;
        } else {
            $data = call_user_func($callback);
            Cache::put($key, $data);
            return Cache::get($key);
        }
    }
}
