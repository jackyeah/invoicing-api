<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/12/12
 * Time: 下午 2:45
 */

namespace App\Http\Helper;


class FormatHandleHelper
{
    /**
     * promotion_code 回傳統一 (join系列)
     *
     * @param array $result
     * @return array
     */
    public static function returnPromotionForJoin(array $result)
    {
        foreach ($result as $key => $val)
        {
            $result[$key]['promotions'] = explode(',', $val['pCode']);
            unset($result[$key]['pCode']);
        }

        return $result;
    }

    /**
     * promotion_code 回傳統一 (with關聯 二維多筆系列)
     *
     * @param array $result
     * @param $with_name
     * @return array
     */
    public static function returnPromotionForWith(array $result, $with_name)
    {
        foreach ($result as $key => $val){
            $result[$key]['promotions'] = array_column($val[$with_name], 'promotion_code');
            unset($result[$key][$with_name]);
        }

        return $result;
    }

    /**
     * promotion_code 回傳統一 (with關聯 單筆 無二維)
     *
     * @param array $result
     * @param $with_name
     * @return array
     */
    public static function returnPromotionOneForWith(array $result, $with_name)
    {
        $result['promotions'] = array_column($result[$with_name], 'promotion_code');
        unset($result[$with_name]);

        return $result;
    }

    public static function delRelationIdForHasMany(array $result, $with_name, $relationIdName)
    {
        foreach ($result as $key => $value)
        {
            foreach ($value[$with_name] as $with_key => $with_value)
            {
                unset($result[$key][$with_name][$with_key][$relationIdName]);
            }
        }

        return $result;
    }

    public static function delRelationIdForBelongTo(array $result, $with_name, $relationIdName)
    {
        foreach ($result as $key => $value)
        {
            unset($result[$key][$with_name][$relationIdName]);
        }

        return $result;
    }
}