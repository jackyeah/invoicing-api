<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/10/16
 * Time: 上午 11:53
 */

namespace App\Http\Repository;


use App\Http\RepositoryProtocol\MultipleEssay;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Input;

class MultipleEssayRepository
{

    public function __construct()
    {
    }

    public function storeUpload()
    {
        $url = Input::get('patch_url');
        try {
            $model = new MultipleEssay();
            $model->patch_url = $url;
            $model->save();
            return true;
        } catch (QueryException $e) {
            return false;
        }
    }

    public function exists($field, $value)
    {
        return MultipleEssay::where($field, $value)->exists();
    }

}