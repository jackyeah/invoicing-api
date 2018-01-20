<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

// 登入
$app->post('admin/login', 'AdminController@login');

$app->group(['middleware' => 'auth'], function () use ($app) {
    // 取得廠商清單
    $app->get('manufacturers', 'ManufacturersController@index');

    // 進貨
    $app->group(['prefix' => 'purchase'], function () use ($app) {
        $app->post('', 'PurchaseController@create');

        // 查看進貨紀錄
        $app->get('', 'PurchaseController@index');
    });

    // 庫存
    $app->group(['prefix' => 'inventory'], function () use ($app) {
        // 取得庫存清單
        $app->get('', 'InventoryController@index');

        // 取得安全庫存清單
        $app->get('safe', 'InventoryController@safe');
    });

    // 設定
    $app->group(['prefix' => 'setting'], function () use ($app) {
        // 取得訂單來源
        $app->get('order_source', 'SettingController@order_source');

        // 取得寄送方式
        $app->get('shipping_method', 'SettingController@shipping_method');

    });
});