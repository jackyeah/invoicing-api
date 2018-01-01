<?php
/**
 * Created by PhpStorm.
 * User: frogyeh
 * Date: 2017/12/26
 * Time: 下午8:56
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Http\Repository;


class ManufacturersController extends InitController
{
    /**
     * 取得廠商清單
     * @return array
     */
    public function index()
    {
        // 驗證參數
        /*$validator = Validator::make(Input::all(), GameList::$search_rules);
        if ($validator->fails()) {
            return $this->fail('59102');
        }*/

        $repository = new Repository\ManufacturersRepository();

        return $this->success($repository->index());
    }
}