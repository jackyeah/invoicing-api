<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/9/29
 * Time: 下午 1:39
 */

namespace App\Http\Helper;

use Illuminate\Support\Facades\Session;


class SessionHelper
{
    //Session 是否存在,不存在時將callback的return value 存入
    public function doesItExist($key, callable $callback)
    {
        $value = Session::get($key);
        if ($value) {
            return $value;
        } else {
            $data = call_user_func($callback);
            Session::put($key, $data);
            return Session::get($key);
        }
    }
}
