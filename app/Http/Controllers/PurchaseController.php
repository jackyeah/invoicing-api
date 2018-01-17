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
use App\Http\Helper\ErrorCode;

class PurchaseController extends InitController
{
    use GetParamsTrait;
    
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
                //'itemNo' => ['required', 'min:3', 'max:20', 'regex:/^((?=.*[A-Za-z0-9]))^.*$/', 'unique:'],
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

        if(empty($array_purchaseDetail)){
            return $this->fail(ErrorCode::VALIDATE_ERROR);
        }

        foreach ($array_purchaseDetail as $item){
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

        if(! $result_id){
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

        if($result){
            return $this->success();
        }else{
            return $this->fail(ErrorCode::UNABLE_WRITE);
        }
    }
}