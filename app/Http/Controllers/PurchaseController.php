<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2018/1/16
 * Time: 上午 10:48
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Http\Repository;
use App\Http\Controllers\Traits\GetParamsTrait;
use App\Http\Services\PurchaseService;
use App\Http\Helper\ErrorCode;
use Illuminate\Validation\Rules\In;

class PurchaseController extends InitController
{
    use GetParamsTrait;

    private $service;

    public function __construct(PurchaseService $purchaseService)
    {
        $this->service = $purchaseService;
    }

    /**
     * 查看進貨紀錄
     * @return array
     */
    public function index()
    {
        $repository_record = new Repository\PurchaseRecordRepository();

        return $this->success($repository_record->index());
    }

    /**
     * 進貨
     * @return array
     */
    public function create()
    {
        // 驗證參數
        $validator = Validator::make(Input::all(),
            [
                'date' => 'required|date',
                'manufacturers' => '',
                'remark' => '',
                'name' => 'required',
                'coast' => 'required|int',
                'price' => 'required|int',
                'purchaseDetail' => 'required'
            ]);
        if ($validator->fails()) {
            return $this->fail(ErrorCode::VALIDATE_ERROR);
        }

        $params = self::getParams(['date', 'manufacturers', 'remark', 'name', 'coast', 'price', 'purchaseDetail']);

        // 檢查進貨細節的資料是否正確
        $array_purchaseDetail = json_decode($params['purchaseDetail'], TRUE);

        if (empty($array_purchaseDetail)) {
            return $this->fail(ErrorCode::VALIDATE_ERROR);
        }

        foreach ($array_purchaseDetail as $item) {
            // 驗證參數
            $validatorDetail = Validator::make($item,
                [
                    'item_no' => ['required', 'min:3', 'max:20', 'regex:/^((?=.*[A-Za-z0-9]))^.*$/', 'unique:product_style'],
                    'style' => 'required',
                    'quality' => 'required|int',
                    'safety_stock' => 'required|int'
                ]);

            if ($validatorDetail->fails()) {
                return $this->fail(ErrorCode::VALIDATE_ERROR);
            }
        }

        // 將資料存至`product`
        $repository = new Repository\ProductRepository();
        $result_id = $repository->create($params['name'], $params['date'], $params['coast'], $params['price']);

        if (!$result_id) {
            return $this->fail(ErrorCode::UNABLE_WRITE);
        }

        // 將要新增至`product_style`的資料補齊
        foreach ($array_purchaseDetail as $key => $arr_datum) {
            $array_purchaseDetail[$key] = array_merge($array_purchaseDetail[$key],
                ['product_id' => $result_id, 'mod_user' => Auth::user()['account'], 'updated_at' => date('Y-m-d H:i:s')]);
        }

        // 將資料存至`product_style`
        $repository_style = new Repository\ProductStyleRepository();
        $result = $repository_style->create($array_purchaseDetail);

        // 取得剛剛存的id
        $result_style_data = $repository_style->getData($result_id);
        $result_style_data = $this->service->dataFormat($result_style_data, $params['date']);

        // 將資料存至`purchase_record`
        $repository_record = new Repository\PurchaseRecordRepository();
        $repository_record->create($result_style_data);

        return $this->success();
    }

    /**
     * 補貨
     * @return array
     */
    public function purchase()
    {
        // 驗證參數
        $validator = Validator::make(Input::all(),
            [
                'id' => 'required|exists:product_style,id',
                'date' => 'required|date',
                'coast' => 'required|int',
                'quantity' => 'required|int'
            ]);
        if ($validator->fails()) {
            return $this->fail(ErrorCode::VALIDATE_ERROR);
        }

        $params = self::getParams(['id', 'date', 'coast', 'quantity']);

        // 寫入進貨紀錄，將資料存至`purchase_record`
        $repository_record = new Repository\PurchaseRecordRepository();
        $repository_record->create_single($params['id'], $params['quantity'], $params['date'], Auth::user()['account']);

        $repository_style = new Repository\ProductStyleRepository();
        // 取得目前產品數量
        $result = $repository_style->getQuality($params['id']);

        // 增加產品數量，更新 `product_style` 的數量
        $repository_style->updateQuantity($params['id'], (int)$params['quantity'] + (int)$result['quality']);

        // 更新 `product` 的成本
        $repository_product = new Repository\ProductRepository();
        $repository_product->updateCoast($result['product_id'], $params['coast']);

        return $this->success();
    }

    /**
     * 編輯進貨紀錄
     * @return array
     */
    public function edit_purchase_data()
    {
        // 驗證參數
        $validator = Validator::make(Input::all(),
            [
                'id' => 'required|exists:purchase_record,id',
                'date' => 'required|date',
                'quantity' => 'required|int|min:0'
            ]);
        if ($validator->fails()) {
            return $this->fail(ErrorCode::VALIDATE_ERROR);
        }

        $params = self::getParams(['id', 'date', 'style', 'quantity']);

        // 從 `purchase_record` 取得該次進貨紀錄的數量
        $repository_record = new Repository\PurchaseRecordRepository();
        $result_record = $repository_record->getQuantityData($params['id']);

        // 從 `product_style` 取得該品項目前的數量
        $repository_style = new Repository\ProductStyleRepository();
        $result_style = $repository_style->getQuality($result_record['product_style_id']);

        // 比對調整後的數量，若為負數，則回傳錯誤訊息
        $newQuantity = $result_style['quality'] + ( $params['quantity'] - $result_record['quantity']);

        if($newQuantity < 0){
            return $this->fail(ErrorCode::INPUT_PARAMS_ERROR);
        }

        // 更新 `purchase_record`
        $result_update_record = $repository_record->update($params['id'], $params['quantity'], $params['date']);

        // 更新 `product_style`
        $result_update_style = $repository_style->updateQuantity($result_record['product_style_id'], $newQuantity);

        if($result_update_record && $result_update_style){
            return $this->success();
        }else{
            return $this->fail(ErrorCode::UNABLE_UPDATE);
        }
    }

    /**
     * 刪除進貨紀錄
     * @return array
     */
    public function delete_purchase_data()
    {
        // 驗證參數
        $validator = Validator::make(Input::all(),
            [
                'id' => 'required|exists:purchase_record,id'
            ]);
        if ($validator->fails()) {
            return $this->fail(ErrorCode::VALIDATE_ERROR);
        }

        // 從 `purchase_record` 取得該次進貨紀錄的數量
        $repository_record = new Repository\PurchaseRecordRepository();
        $result_record = $repository_record->getQuantityData(Input::get('id'));

        // 從 `product_style` 取得該品項目前的數量
        $repository_style = new Repository\ProductStyleRepository();
        $result_style = $repository_style->getQuality($result_record['product_style_id']);

        // 比對調整後的數量，若為負數，則回傳錯誤訊息
        $newQuantity = $result_style['quality'] + ( 0 - $result_record['quantity']);

        if($newQuantity < 0){
            return $this->fail(ErrorCode::INPUT_PARAMS_ERROR);
        }

        // 刪除 `purchase_record` 紀錄
        $result_delete = $repository_record->delete(Input::get('id'));

        // 更新 `product_style`
        $result_update_style = $repository_style->updateQuantity($result_record['product_style_id'], $newQuantity);

        if($result_delete && $result_update_style){
            return $this->success();
        }else{
            return $this->fail(ErrorCode::UNABLE_UPDATE);
        }

    }
}