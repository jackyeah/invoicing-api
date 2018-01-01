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

//因應更改API網域加上前綴
//$app->group(['prefix' => ''], function () use ($app) {
    //需要身份驗證的後台功能
    $app->group(['prefix' => 'backend', 'middleware' => 'auth'], function () use ($app) {
    //$app->group(['prefix' => 'backend'], function () use ($app) {

        $app->post('upload/image', 'UploadController@image');

        //upload news images
        $app->post('upload/news_img', 'UploadController@newsImg');

        //upload banner image
        $app->post('upload/banner_img', 'UploadController@bannerImg');

        //upload game image
        $app->post('upload/game_img', 'UploadController@gameImg');

        //upload content image
        $app->post('upload/content_img', 'UploadController@contentImg');

        // 推廣站清單
        $app->post('promotion', 'PromotionController@index');

        // 最新報導類別清單
        $app->get('news/type_list', 'NewsController@getNewsTypeList');

        // 最新報導清單
        $app->post('news', 'NewsController@index');

        // 取得單筆最新報導詳細資料
        $app->post('news/content', 'NewsController@getNewsContent');

        // 新增最新報導
        $app->post('news/create', 'NewsController@create');

        // 編輯最新報導
        $app->post('news/update', 'NewsController@update');

        // 刪除最新報導
        $app->post('news/delete', 'NewsController@delete');

        $app->post('admin', 'AdminController@index');

        $app->post('admin/update', 'AdminController@update');

        //編輯管理者可使用功能類別
        $app->post('system_feature/edit', 'SystemFeatureController@edit');

        //功能大類清單和該帳號權限
        $app->post('system_feature/kind_code_status', 'SystemFeatureController@kindCodeStatus');

        //取得該使用者可使用功能選單
        $app->get('system_feature/feature_menu', 'SystemFeatureController@featureMenu');

        //管理者帳號清單
        $app->get('admin/get_list', 'AdminController@getList');

        //註冊管理者
        $app->post('admin/register', 'AdminController@register');

        // 取得遊戲清單
        $app->post('game', 'GameController@index');

        // 新增遊戲資料
        $app->post('game/create', 'GameController@create');

        // 編輯遊戲資料
        $app->post('game/update', 'GameController@update');

        // 刪除遊戲資料
        $app->post('game/delete', 'GameController@delete');

        // 取得排行榜類型清單
        $app->get('ranking/type', 'RankingController@getTypeList');

        // 取得排行榜清單
        $app->post('ranking', 'RankingController@index');

        // 編輯排行榜清單
        $app->post('ranking/update', 'RankingController@update');

        // 刪除排行榜清單
        $app->post('ranking/delete', 'RankingController@delete');

        // 取得各站活動清單
        $app->get('festival', 'FestivalController@index');

        // 各站活動開啟/關閉
        $app->post('festival/update', 'FestivalController@update');

        // 橫幅
        $app->group(['prefix' => 'banner'], function () use ($app) {
            // 取得橫幅清單
            $app->post('', 'BannerController@index');

            // 取得單筆橫幅詳細資料
            $app->post('detail', 'BannerController@detail');

            // create banner
            $app->post('create', 'BannerController@create');

            // update banner
            $app->post('update', 'BannerController@update');

            // 刪除橫幅資料以及圖片
            $app->post('delete', 'BannerController@delete');
        });

        // 維護
        $app->group(['prefix' => 'maintain'], function () use ($app) {
            // 取得站台維護清單
            $app->get('', 'MaintainController@index');

            // 新增維護資料
            $app->post('create', 'MaintainController@create');

            // 更新維護資料
            $app->post('update', 'MaintainController@update');

            // 刪除維護資料
            $app->post('delete', 'MaintainController@delete');
        });

        // 取得廠商清單
        $app->get('manufacturers', 'ManufacturersController@index');
    });

    $app->post('admin/login', 'AdminController@login');
//});


