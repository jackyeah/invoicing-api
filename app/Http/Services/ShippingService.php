<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2018/1/21
 * Time: 上午 9:14
 */

namespace App\Http\Services;

use Illuminate\Support\Facades\Auth;


class ShippingService
{

    /*
     * array(4) {
    ["id"]=>
    string(1) "6"
    ["quantity"]=>
    string(1) "1"
    ["price"]=>
    string(3) "130"
    ["purchase_quality"]=>
    int(16)
  }
     * */
    /**
     * 調整資料格式
     * @param $data
     * @param $shippingID
     * @param $source_id
     * @param $shipping_method_id
     * @return mixed
     */
    public function dataFormat($data, $shippingID, $source_id, $shipping_method_id)
    {
        foreach ($data as $key => $datum){
            $data[$key]['shippingID'] = $shippingID;
            $data[$key]['source_id'] = $source_id;
            $data[$key]['shipping_method_id'] = $shipping_method_id;
            $data[$key]['product_style_id'] = $datum['id'];
            $data[$key]['quantity'] = $datum['quantity'];
            $data[$key]['price'] = $datum['price'];
            $data[$key]['mod_user'] = Auth::user()['account'];
            $data[$key]['updated_at'] = date('Y-m-d H:i:s');

            unset($data[$key]['id']);
            unset($data[$key]['purchase_quality']);
        }
        return $data;
    }
}