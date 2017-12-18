<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/11/14
 * Time: 下午 2:37
 */

namespace App\Http\Services;

use Illuminate\Support\Facades\File;

class NewsReportService
{
    /**
     * 刪除圖檔
     *
     * @param $file : 檔名
     * @param $img_path : 路徑
     */
    public function deleteImg($file, $img_path)
    {
        return File::delete($img_path . '/' . $file);
    }
}