<?php
/**
 * Created by PhpStorm.
 * User: dev
 * <<<<<<< HEAD
 * Date: 2017/11/24
 * Time: 上午 10:40
 * =======
 * Date: 2017/12/1
 * Time: 上午 11:33
 * >>>>>>> add/uploadImg/ed
 */

namespace App\Http\Services;


use App\Http\Controllers\Traits\UploadFileTrait;
use App\Http\Repository\GameListRepository;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use App\Http\Helper\ErrorCode;
use App\Http\Repository\BannerRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class UploadService
{
    /**
     * 檢查是否更新圖片檔
     * 有更新圖片則刪除舊的圖檔
     *
     * @param $file
     * @param $img_path
     * @param $old_file
     * @return bool|string
     */
    public function checkImageUpdate($file, $img_path, $old_file)
    {
        if (Input::hasFile($file)) {
            $upload = UploadFileTrait::uploadFile($file, $img_path);

            if ($upload == false) {
                return false;
            }
            File::delete($img_path . '/' . $old_file);
            return $upload;
        } else {
            return $old_file;
        }
    }

    /**
     * 刪除圖檔
     *
     * @param $file : 檔名
     * @param $img_path : 路徑
     */
    public function deleteImg($file, $img_path)
    {
        File::delete($img_path . '/' . $file);
    }


    public function checkType($type)
    {
        $imagePath = config('define.img_path.' . $type);
        if (! $imagePath) {
            Log::error('check config img_path');
            return false;
        }
        return true;
    }

    public function fileInfo($imageType, $fileName)
    {
        $result['domain'] = URL::to('/');
        $result['path'] = config('define.img_server.' . $imageType);
        $result['file_name'] = $fileName;
        return $result;
    }

    /**
     * @param $id
     * @param $type
     * @return mixed
     */
    public function banner($id, $type)
    {
        $repository = new BannerRepository();
        return $repository->find($id)->$type;
    }

    /**
     * @param $id
     * @param $imageType
     * @param $fileName
     * @return bool
     */
    public function updateBanner($id, $imageType, $fileName)
    {
        $repository = new BannerRepository();
        return $repository->updateImg($id, $imageType, $fileName);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function game($id)
    {
        $repository = new GameListRepository();
        return $repository->find($id)->pic;
    }

    /**
     * @param $id
     * @param $fileName
     * @return bool
     */
    public function updateGameImg($id, $fileName)
    {
        $repository = new GameListRepository();
        return $repository->updateImg($id, $fileName);
    }

}