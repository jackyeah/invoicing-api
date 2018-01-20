<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2018/1/20
 * Time: 下午 3:55
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Http\Repository;
use App\Http\Controllers\Traits\GetParamsTrait;
use App\Http\Services\PurchaseService;
use App\Http\Helper\ErrorCode;

class SettingController extends InitController
{
    use GetParamsTrait;

    /**
     * 取得訂單來源
     * @return array
     */
    public function order_source()
    {
        $repository = new Repository\OrderSourceRepository();

        return $this->success($repository->index());
    }

    /**
     * 取得寄送方式
     * @return array
     */
    public function shipping_method()
    {
        $repository = new Repository\ShippingMethodRepository();

        return $this->success($repository->index());
    }

}