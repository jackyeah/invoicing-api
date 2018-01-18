<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2018/1/18
 * Time: 下午 2:59
 */

namespace App\Http\Services;

use Illuminate\Support\Facades\Auth;

class PurchaseService
{
    /**
     * 調整資料格式
     * @param $data
     * @param $purchaseDate
     * @return mixed
     */
    public function dataFormat($data, $purchaseDate)
    {
        foreach ($data as $key => $datum){
            $data[$key]['product_style_id'] = $datum['id'];
            $data[$key]['purchase_time'] = $purchaseDate;
            $data[$key]['mod_user'] = Auth::user()['account'];
            $data[$key]['quantity'] = $datum['quality'];
            $data[$key]['updated_at'] = date('Y-m-d H:i:s');

            unset($data[$key]['id']);
            unset($data[$key]['quality']);
        }
        return $data;
    }
}