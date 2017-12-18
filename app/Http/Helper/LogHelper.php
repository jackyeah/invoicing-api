<?php


namespace App\Http\Helper;


class LogHelper
{
    public static function toFormatString($string)
    {
        $class = debug_backtrace()[1]['class'];
        $line = debug_backtrace()[0]['line'];
        return $class . '(' . $line . ')' . $string;

    }
}