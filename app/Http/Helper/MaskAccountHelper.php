<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/9/13
 * Time: 上午 11:02
 */

namespace App\Http\Helper;


class MaskAccountHelper
{
    /**
     * @param array $periodRank
     */
    //Shelter Account
    //Format [['account'=>''],['account'=>'']]
    public static function MaskAccount(array &$periodRank)
    {
        $shelter = '*****';

        foreach ($periodRank as $key => &$value) {
            $subAccount = substr($value['account'], -5);
            $length = strlen($subAccount);
            if ($length === 5) {
                $value['account'] = $shelter . $subAccount;
            } elseif ($length < 5) {
                $add = 5 - (int)$length;
                $mark = '';
                for ($i = $add; $i > 0; $i--) {
                    $mark .= '*';
                }
                $value['account'] = $shelter . $mark . $subAccount;
            } else {
                $value['account'] = $shelter;
            }
        }
    }
    /**
     * @param string $rule
     * @param array $periodRank
     */
    //Shelter Account
    //Format [['account'=>''],['account'=>'']]
    public static function MaskUserID($rule, array &$periodRank)
    {
        $shelter = '*****';

        foreach ($periodRank as $key => &$value) {
            $subAccount = substr($value[$rule], -5);
            $length = strlen($subAccount);
            if ($length === 5) {
                $value[$rule] = $shelter . $subAccount;
            } elseif ($length < 5) {
                $add = 5 - (int)$length;
                $mark = '';
                for ($i = $add; $i > 0; $i--) {
                    $mark .= '*';
                }
                $value[$rule] = $shelter . $mark . $subAccount;
            } else {
                $value[$rule] = $shelter;
            }
        }
    }
}