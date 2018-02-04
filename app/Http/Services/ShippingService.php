<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2018/1/21
 * Time: 上午 9:14
 */

namespace App\Http\Services;

use Illuminate\Support\Facades\Auth;
use App\Http\Repository;

class ShippingService
{
    /**
     * 調整存至 shipping_record 出貨紀錄的資料格式
     * @param $data
     * @param $shippingID
     * @return mixed
     */
    public function dataFormat($data, $shippingID)
    {
        foreach ($data as $key => $datum){
            $data[$key]['shipping_list_id'] = $shippingID;
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

    /**
     * 調整取得出貨紀錄的資料格式
     * @param $data
     * @return mixed
     */
    public function shippingListDataFormat($data)
    {
        $repository_style = new Repository\ProductStyleRepository();
        if (!empty($data)){
            foreach ($data as $key => $datum){
                foreach ($datum['shipping_record'] as $subKey => $item){
                    $productDetail = $repository_style->getNameInfo($item['product_style_id']);
                    $data[$key]['product_detail'][$subKey] = array_merge($productDetail, $data[$key]['shipping_record'][$subKey]);

                    unset($data[$key]['product_detail'][$subKey]['shipping_list_id']);
                    unset($data[$key]['product_detail'][$subKey]['product_style_id']);
                }
                unset($data[$key]['shipping_record']);
            }
        }
        return $data;
    }
}