<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


trait UploadFileTrait
{
    /**
     * @return array
     */
    private static function rules()
    {
        return [
            'web_image' => 'image|mimes:jpeg,png,jpg|max:12240',
            'mobile_image' => 'image|mimes:jpeg,png,jpg|max:12240',
            'image' => 'image|mimes:jpeg,png,jpg|max:12240',
            'content' => 'image|mimes:jpeg,png,jpg|max:12240',
            'pic_web' => 'image|mimes:jpeg,png,jpg|max:12240',
            'pic_mobile' => 'image|mimes:jpeg,png,jpg|max:12240',
        ];
    }

    /**
     * @param $type
     * @param $savePath
     * @return bool|string
     */
    public static function uploadFile($type, $savePath)
    {
        //get file
        $file = Input::file($type);
        if ($file) {
            //Validate File ,type max
            $validate = Validator::make(array_column(Input::allFiles(), $type), self::rules());
            if ($validate->fails()) {
                return false;
            }

            $fileName = explode('.', $file->getClientOriginalName());
            $name = time() . '_' . str_random(5) . '.' . end($fileName);
            try {
                $file->move($savePath, $name);
                return $name;
            } catch (FileException $e) {
                Log::error($e->getMessage());
            }
        }
        return false;
    }

    public function deleteFile($path, $fileName)
    {
        File::delete($path . '/' . $fileName);
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
}