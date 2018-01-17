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
    });
});