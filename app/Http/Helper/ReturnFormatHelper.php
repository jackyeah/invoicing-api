<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/9/11
 * Time: 下午 5:45
 */

namespace App\Http\Helper;


class ReturnFormatHelper
{
    const successCode = '1';

    private static $_response = [
        'mt' => '0',
        'error_msg' => '',
        'error_code' => '0',
        'result' => []
    ];

    /**
     * @return array
     */
    public function getDefaultResponse()
    {
        return self::$_response;
    }

    /**
     * @param $data
     * @return array
     */
    public static function success($data)
    {
        $result = self::$_response;
        $result['error_code'] = self::successCode;
        $result['result'] = $data;
        return $result;
    }

    /**
     * @param $errorCode
     * @param array $data
     * @return array
     */
    public static function fail($errorCode, $data, $message = [])
    {
        $result = self::$_response;
        $result['error_code'] = $errorCode;
        $result['result'] = $data;
        $result['error_msg'] = $message;
        return $result;
    }

}