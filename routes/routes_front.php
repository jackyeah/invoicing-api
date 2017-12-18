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

$app->group(['prefix' => 'cq9-api/frontend', 'middleware' => 'maintain'], function () use ($app) {

    // 取得推廣站代碼
    $app->get('Promotion', 'PromotionController@getPromotionCode');

    // 最新報導清單
    $app->get('news', 'NewsController@getNewsList_FrontEnd');

    // 取得單筆最新報導詳細資料
    $app->post('news/content', 'NewsController@getNewsContent_FrontEnd');

    // 橫幅清單
    $app->get('banner', 'BannerController@getBannerList_FrontEnd');

    // 熱門遊戲排行
    $app->get('ranking/hot', 'RankingController@getList_hot_FrontEnd');

    // 遊戲大獎次數 - 100倍
    $app->get('ranking/big/100', 'RankingController@getList_big_100_FrontEnd');

    // 遊戲大獎次數 - 200倍
    $app->get('ranking/big/200', 'RankingController@getList_big_200_FrontEnd');

    // 遊戲大獎次數 - 500倍
    $app->get('ranking/big/500', 'RankingController@getList_big_500_FrontEnd');

    // 遊戲大獎次數 - 1000倍
    $app->get('ranking/big/1000', 'RankingController@getList_big_1000_FrontEnd');

    // 玩家分數排行
    $app->get('ranking/fraction', 'RankingController@getList_fraction_FrontEnd');

    // 玩家倍數排行
    $app->get('ranking/multiple', 'RankingController@getList_multiple_FrontEnd');

    // 取得活動狀態
    $app->get('festival', 'FestivalController@getList_FrontEnd');

    // 取得遊戲清單
    $app->get('game', 'GameController@getList_FrontEnd');

});
