<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/12/6
 * Time: 下午 5:43
 */

namespace App\Http\Helper;

use App\Http\Repository\PromotionStationRepository;

class PromotionHelper
{
    /**
     * 驗證promotion_code(json)是否符合規範的格式並確認資料庫是否有值
     *
     * @param $promotion_code
     * @return bool
     */
    public function ValidatePromotionCode($promotion_code)
    {
        //判斷是否有pCode參數
        $code_array = array();
        foreach (json_decode($promotion_code, true) as $value)
        {
            if(isset($value['pCode'])){
                $code_array[] = $value['pCode'];
            } else {
                return false;
            }
        }

        //判斷傳入的code,資料庫是否有資料
        $repository = new PromotionStationRepository();
        $count = $repository->getCountByCodes($code_array);
        if($count == count($code_array)) {
            return true;
        } else {
            return false;
        }
    }
}