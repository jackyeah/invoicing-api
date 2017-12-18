<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\GetParamsTrait;
use App\Http\Controllers\Traits\UploadFileTrait;
use App\Http\Helper\ErrorCode;
use App\Http\Repository\NewReportRepository;
use App\Http\Services\UploadService;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;


class UploadController extends InitController
{
    use UploadFileTrait;
    use GetParamsTrait;

    protected $service;

    public function __construct(UploadService $service)
    {
        $this->service = $service;
    }

    public function image()
    {
        $imageType = Input::get('type');
        if (! $this->checkType($imageType)) {
            return $this->fail(ErrorCode::ILLEGAL_FILE_TYPE);
        }

        $imagePath = config('define.img_path.' . $imageType);
        $fileName = $this->uploadFile('image', $imagePath);
        if ($fileName == false) {
            Log::error('File upload fail');
            return $this->fail(ErrorCode::FILE_UPLOAD_FAIL);
        }
        return $this->success($this->fileInfo($imageType, $fileName));
    }

    /**
     * @return array
     */
    public function bannerImg()
    {
        $imageType = Input::get('type');
        if (! $this->checkType($imageType)) {
            return $this->fail(ErrorCode::ILLEGAL_FILE_TYPE);
        }

        $imagePath = config('define.img_path.' . $imageType);
        $fileName = $this->uploadFile('image', $imagePath);
        if ($fileName == false) {
            Log::error('File upload fail');

            return $this->fail(ErrorCode::FILE_UPLOAD_FAIL);
        }
        return $this->success($this->fileInfo($imageType, $fileName));
    }

    /**
     * 上傳圖片
     *
     * @return array|string 圖片位置
     */
    public function contentImg()
    {

        if (! $this->service->checkType('content')) {
            return $this->fail(ErrorCode::ILLEGAL_FILE_TYPE);
        }
        $imagePath = config('define.img_path.content');
        $fileName = $this->uploadFile('image', $imagePath);
        if ($fileName == false) {
            Log::error('File upload fail');
            $this->deleteFile($imagePath, $fileName);
            return $this->fail(ErrorCode::FILE_UPLOAD_FAIL);
        }
        $result = $this->service->fileInfo('content', $fileName);
        return $this->success($result);
    }

    /**
     * @return array
     */
    public function newsImg()
    {
        if (! $newsId = (integer)Input::get('id')) {
            return $this->fail(ErrorCode::PARAMS_ERROR);
        };
        // 上傳圖片
        $fileName = $this->uploadFile('image', config('define.img_path.news'));
        if ($fileName == false) {
            return $this->fail('54201');
        }
        return $this->success($this->service->fileInfo('news', $fileName));
    }

    /**
     * @return array
     */
    public function gameImg()
    {
        if (! $path = config('define.img_path.game')) {
            Log::erorr('check config');
            return $this->fail(ErrorCode::NO_CONFIG_VALUE);
        };
        $fileName = $this->uploadFile('image', $path);
        if ($fileName == false) {
            $this->deleteFile($path, $fileName);
            Log::error('File upload fail');
            return $this->fail(ErrorCode::FILE_UPLOAD_FAIL);
        }

        return $this->success($this->service->fileInfo('game', $fileName));
    }


}