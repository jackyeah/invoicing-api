<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/9/12
 * Time: 下午 3:47
 */

namespace App\Http\Controllers;

use App\Http\Helper\ErrorCode;
use App\Http\Helper\HttpClientHelper;
use App\Http\Helper\LogHelper;
use App\Http\Helper\MaskAccountHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Traits\GetParamsTrait;
use Illuminate\Support\Facades\Input;


class RankController extends InitController
{
    use GetParamsTrait;

    public function __construct()
    {

    }

    /**
     * @return array
     */
    public function getRank()
    {
        $ownerId = Input::get('ownerId');;
        if (! $ownerId) {
            Log::error(LogHelper::toFormatString('Does not exist site token'));
            return $this->fail(ErrorCode::NONE_SITE);
        }
        $rankRedisKey = $ownerId . 'rank';
        $timeoutRedisKey = $ownerId . 'rank_timeout';
        $newTime = strtotime(date('Y-m-d H' . ':00:02'));
        if ($rank = Redis::get($rankRedisKey)) {
            //last update time
            $oldTime = Redis::get($timeoutRedisKey);
            //exit update time && not over 1 hour
            if ($oldTime && $newTime <= $oldTime) {
                return $this->success(json_decode($rank, true));
            }
        }

        $client = new HttpClientHelper();
        //參數$
        $params = self::getParams(['period', 'limit']);
        $params['ownersid'] = $ownerId;
        //跟cp取得資料
        $rankData = $client->sendPost(config('define.cypress.url.getRank'), $params);
        //to Array
        $rankDataArray = json_decode($rankData, true);
        //使用collect function
        $collection = collect($rankDataArray);
        //取status的資料
        $status = $collection->get('status');
        //code 等於0 ,成功跟cp取得資料
        if ($status['code'] == 0) {
            $data = $collection->get('data');
            //馬賽克Account
            MaskAccountHelper::maskAccount($data['periodrank']);
            $jsonData = json_encode($data['periodrank']);
            Redis::set($rankRedisKey, $jsonData);
            Redis::set($timeoutRedisKey, $newTime);
            return $this->success($data['periodrank']);
        } else {
            Log::error(LogHelper::toFormatString('Please check Cypress API rules'));
            return $this->fail(ErrorCode::CONNECTION_CYPRESS_ERROR);
        }
    }
}