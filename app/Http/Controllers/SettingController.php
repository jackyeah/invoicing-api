<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2018/1/20
 * Time: 下午 3:55
 */

namespace App\Http\Controllers;

use App\Http\RepositoryProtocol\OrderSource;
use App\Http\RepositoryProtocol\ShippingMethod;
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
    public function get_order_source()
    {
        $repository = new Repository\OrderSourceRepository();

        return $this->success($repository->index());
    }

    /**
     * 新增訂單來源
     * @return array
     */
    public function create_order_source()
    {
        // 驗證參數
        $validator = Validator::make(Input::all(), OrderSource::$create_rules);
        if ($validator->fails()) {
            return $this->fail(ErrorCode::VALIDATE_ERROR);
        }

        $repository = new Repository\OrderSourceRepository();

        if($repository->create(Input::get('name'))){
            return $this->success();
        }else{
            return $this->fail(ErrorCode::UNABLE_WRITE);
        }
    }

    /**
     * 更新訂單來源
     * @return array
     */
    public function update_order_source()
    {
        // 驗證參數
        $validator = Validator::make(Input::all(), OrderSource::$update_rules);
        if ($validator->fails()) {
            return $this->fail(ErrorCode::VALIDATE_ERROR);
        }

        $repository = new Repository\OrderSourceRepository();

        if($repository->update(Input::get('id'), Input::get('name'))){
            return $this->success();
        }else{
            return $this->fail(ErrorCode::UNABLE_WRITE);
        }
    }

    /**
     * 刪除訂單來源
     * @return array
     */
    public function delete_order_source()
    {
        // 驗證參數
        $validator = Validator::make(Input::all(), OrderSource::$delete_rules);
        if ($validator->fails()) {
            return $this->fail(ErrorCode::VALIDATE_ERROR);
        }

        $repository = new Repository\OrderSourceRepository();

        if($repository->delete(Input::get('id'))){
            return $this->success();
        }else{
            return $this->fail(ErrorCode::UNABLE_WRITE);
        }
    }

    /**
     * 取得寄送方式
     * @return array
     */
    public function get_shipping_method()
    {
        $repository = new Repository\ShippingMethodRepository();

        return $this->success($repository->index());
    }

    /**
     * 新增寄送方式
     * @return array
     */
    public function create_shipping_method()
    {
        // 驗證參數
        $validator = Validator::make(Input::all(), ShippingMethod::$create_rules);
        if ($validator->fails()) {
            return $this->fail(ErrorCode::VALIDATE_ERROR);
        }

        $repository = new Repository\ShippingMethodRepository();

        if($repository->create(Input::get('name'))){
            return $this->success();
        }else{
            return $this->fail(ErrorCode::UNABLE_WRITE);
        }

    }

    /**
     * 更新訂單來源
     * @return array
     */
    public function update_shipping_method()
    {
        // 驗證參數
        $validator = Validator::make(Input::all(), ShippingMethod::$update_rules);
        if ($validator->fails()) {
            return $this->fail(ErrorCode::VALIDATE_ERROR);
        }

        $repository = new Repository\ShippingMethodRepository();

        if($repository->update(Input::get('id'), Input::get('name'))){
            return $this->success();
        }else{
            return $this->fail(ErrorCode::UNABLE_WRITE);
        }
    }

    /**
     * 刪除寄送方式
     * @return array
     */
    public function delete_shipping_method()
    {
        // 驗證參數
        $validator = Validator::make(Input::all(), ShippingMethod::$delete_rules);
        if ($validator->fails()) {
            return $this->fail(ErrorCode::VALIDATE_ERROR);
        }

        $repository = new Repository\ShippingMethodRepository();

        if($repository->delete(Input::get('id'))){
            return $this->success();
        }else{
            return $this->fail(ErrorCode::UNABLE_WRITE);
        }
    }

}