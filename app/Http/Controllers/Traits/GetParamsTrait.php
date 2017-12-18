<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Support\Facades\Input;

trait GetParamsTrait
{

    /**
     * @param array $params
     * @return array
     */
    //依照value取得Input
    public static function getParams(Array $params)
    {
        $define = [];
        if ($params) {
            foreach ($params as $value) {
                $define[$value] = Input::get($value);
            }
        }
        return $define;
    }
}