<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/9/1
 * Time: 下午 6:17
 */

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Helper\ReturnFormatHelper;
use Illuminate\Support\Facades\Input;

abstract class InitController extends BaseController implements ControllerInterface
{
    /**
     * @param array $data
     * @return array
     */
    public function success($data = [])
    {
        return ReturnFormatHelper::success($data);
    }

    /**
     * @param $errorCode
     * @param array $data
     * @return array
     */
    public function fail($errorCode, $data = [], $message = [])
    {
        return ReturnFormatHelper::fail($errorCode, $data, $message);
    }

}