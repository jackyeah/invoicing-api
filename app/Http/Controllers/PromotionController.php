<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/10/30
 * Time: 上午 9:59
 */

namespace App\Http\Controllers;

use App\Http\Repository;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Http\RepositoryProtocol;
use App\Http\Helper\ErrorCode;


class PromotionController extends InitController
{
    public function __construct()
    {
        //
    }

    /**
     * 取得推廣站清單
     *
     * @return array
     */
    public function index()
    {
        // 驗證參數
        $validator = Validator::make(Input::all(), RepositoryProtocol\PromotionStation::$searchRules);
        if ($validator->fails()) {
            return $this->fail(ErrorCode::VALIDATE_ERROR);
        }

        // 取得推廣站清單
        $repository = new Repository\PromotionStationRepository();
        $result_data = $repository->getAllList(Input::get('name'), Input::get('status'));

        return $this->success($result_data);
    }

    /**
     * 根據網域名稱，取得推廣站代碼
     *
     * @return array
     */
    public function getPromotionCode()
    {
        // 驗證參數
        $validator = Validator::make(['url' => Input::header('site-domain'), 'status' => 1],
            RepositoryProtocol\PromotionStation::$searchRules);
        if ($validator->fails()) {
            return $this->fail(ErrorCode::VALIDATE_ERROR);
        }

        // 取得推廣站清單
        $repository = new Repository\PromotionStationRepository();
        $result_data = $repository->getDataByUrl(Input::header('site-domain'), 1);

        return $this->success($result_data);
    }
}