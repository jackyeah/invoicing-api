<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2018/1/24
 * Time: 下午 2:05
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Http\Repository;
use App\Http\Controllers\Traits\GetParamsTrait;
use App\Http\Services\ShippingService;
use App\Http\Helper\ErrorCode;

class ShippingController extends InitController
{
    use GetParamsTrait;

    private $service;

    public function __construct(ShippingService $shippingService)
    {
        $this->service = $shippingService;
    }

    /**
     * 賣出產品
     * @return array
     */
    public function sell()
    {
        // 驗證參數
        $validator = Validator::make(Input::all(),
            [
                'date' => 'required|date',
                'order_source_id' => 'required|exists:order_source,id',
                'shipping_method_id' => 'required|exists:shipping_method,id',
                'shippingDetail' => 'required'
            ]);
        if ($validator->fails()) {
            return $this->fail(ErrorCode::VALIDATE_ERROR);
        }

        $params = self::getParams(['date', 'order_source_id', 'shipping_method_id', 'shippingDetail']);

        // 檢查要出貨的產品數量是否足夠
        $dataList = json_decode($params['shippingDetail'], TRUE);

        if (empty($dataList)) {
            return $this->fail(ErrorCode::VALIDATE_ERROR);
        }

        $repository_style = new Repository\ProductStyleRepository();
        $updateData = array();
        foreach ($dataList as $key => $item){
            $result_style = $repository_style->getQuality($item['id']);

            if((int)$item['quantity'] > (int)$result_style['quality']){
                return $this->fail(ErrorCode::VALIDATE_ERROR);
            }

            $dataList[$key]['purchase_quality'] = (int)$result_style['quality'] - (int)$item['quantity'];

            $updateData[] = ['id' => $item['id'],
                'purchase_quality' => (int)$result_style['quality'] - (int)$item['quantity'],
                'shipping_quality' => $item['quantity'], 'shipping_price' => $item['price']];
        }

        $repository_record = new Repository\ShippingRecordRepository();
        foreach ($dataList as $key => $datum){
            // 更改庫存數量
            $repository_style->updateQuantity($datum['id'], $datum['purchase_quality']);
        }

        // 存至出貨紀錄
        $dataList = $this->service->dataFormat($dataList, time(), $params['order_source_id'], $params['shipping_method_id']);
        $repository_record->create($dataList);

        return $this->success();
    }

    public function index()
    {

    }
}