<?php
/**
 * Created by PhpStorm.
 * User: augus
 * Date: 2017/3/30
 * Time: 上午 11:44
 */

return [
    'driver' => env('SESSION_DRIVER', 'redis'),//默认使用file驱动，你也可以在.env中配置
    'lifetime' => 120,//缓存失效时间
    'expire_on_close' => false,
    'encrypt' => false,
    'files' => storage_path('framework/session'),//file缓存保存路径
    'connection' => null,
    'table' => 'sessions',
    'lottery' => [2, 100],
    'cookie' => 'laravel_session',
    'path' => '/',
    'domain' => null,
    'secure' => false,
];
