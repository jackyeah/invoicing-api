<?php

namespace App\Http\Helper;

use Illuminate\Support\Facades\Redis;


class RedisHelper
{
    //Redis 是否存在,不存在時將callback的return value 存入
    /**
     * @param $key
     * @param callable $callback
     * @return mixed
     */
    public function doesItExist($key, callable $callback)
    {
        $value = Redis::get($key);
        if ($value) {
            return $value;
        }

        $data = call_user_func($callback);
        Redis::set($key, $data);
        return Redis::get($key);
    }
}
