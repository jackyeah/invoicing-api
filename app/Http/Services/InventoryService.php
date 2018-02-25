<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2018/2/24
 * Time: 下午 11:21
 */

namespace App\Http\Services;


class InventoryService
{
    public function filterSafetyStock($subtraction, $data)
    {
        $result = array();
        foreach ($data as $datum){
            if($datum['quality'] - $subtraction <= $datum['safety_stock']){
                $result[] = $datum;
            }
        }

        return $result;

    }

}