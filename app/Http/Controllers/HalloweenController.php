<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/10/16
 * Time: 下午 1:42
 */

namespace App\Http\Controllers;

use App\Http\Helper\LogHelper;
use App\Http\Repository\MultipleEssayRepository;
use App\Http\RepositoryProtocol\MultipleEssay;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Helper\ErrorCode;

class HalloweenController extends InitController
{
    public $repository;

    public function __construct()
    {
        $this->repository = new MultipleEssayRepository();
    }

    public function index()
    {
        $url = Input::get('patch_url');
        $validator = Validator::make(Input::all(), MultipleEssay::$rules);
        //驗證url
        if ($validator->fails()) {
            //url 格式錯誤
            Log::error(LogHelper::toFormatString('Does not conform to the format'));
            return $this->fail(ErrorCode::VALIDATE_ERROR);
        }

        //url 不能重複
        if ($this->repository->exists('patch_url', $url)) {
            Log::error(LogHelper::toFormatString('Repeat Url'));
            return $this->fail('56101');
        } else {
            //insert
            $result = $this->repository->storeUpload();
            if ($result) {
                return $this->success();
            } else {
                Log::error(LogHelper::toFormatString('DB insert error'));
                return $this->fail('56102');
            }
        }
    }

}