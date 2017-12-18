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

$app->group(['prefix' => 'cq9-api'], function () use ($app) {

    $app->group(['prefix' => 'halloween'], function () use ($app) {

        $app->post('upload', 'HalloweenController@index');

    });

    $app->group(['prefix' => 'rank', 'middleware' => 'validate'], function () use ($app) {

        $app->post('getRank', 'RankController@getRank');

    });
});

