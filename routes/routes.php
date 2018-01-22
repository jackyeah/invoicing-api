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

        // 舊商品補貨
        $app->put('', 'PurchaseController@purchase');
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
        $app->get('order_source', 'SettingController@get_order_source');

        // 新增訂單來源
        $app->post('order_source', 'SettingController@create_order_source');

        // 更新訂單來源
        $app->put('order_source', 'SettingController@update_order_source');

        // 刪除訂單來源
        $app->delete('order_source', 'SettingController@delete_order_source');

        // 取得寄送方式
        $app->get('shipping_method', 'SettingController@get_shipping_method');

        // 新增寄送方式
        $app->post('shipping_method', 'SettingController@create_shipping_method');

        // 更新寄送方式
        $app->put('shipping_method', 'SettingController@update_shipping_method');

        // 刪除寄送方式
        $app->delete('shipping_method', 'SettingController@delete_shipping_method');
    });
});