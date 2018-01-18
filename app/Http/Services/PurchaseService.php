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
    public function dataFormat($data)
    {
        foreach ($data as $key => $datum){
            $data[$key]['product_style_id'] = $datum['id'];
            $data[$key]['purchase_time'] = $datum['updated_at'];
            $data[$key]['mod_user'] = Auth::user()['account'];
            $data[$key]['quantity'] = $datum['quality'];
            $data[$key]['updated_at'] = date('Y-m-d H:i:s');

            unset($data[$key]['id']);
            unset($data[$key]['quality']);
        }
        return $data;
    }
}