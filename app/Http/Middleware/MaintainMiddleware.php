<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/11/6
 * Time: 上午 10:50
 */

namespace App\Http\Middleware;


use App\Http\Helper\WhiteListHelper;
use App\Http\Repository\MaintainRepository;
use Closure;
use Illuminate\Support\Facades\Input;
use App\Http\Repository\PromotionStationRepository;
use App\Http\Helper\ReturnFormatHelper;
use App\Http\Helper\ErrorCode;

class MaintainMiddleware
{
    public $repository;

    public function __construct()
    {
        $this->repository = new MaintainRepository();
    }

    public function handle($request, Closure $next)
    {
        // ip是否在白名單中
        $ip = Input::ip();

        if (in_array($ip, WhiteListHelper::getWhiteIp())) {
            return $next($request);
        }

        // 判斷是否此token是否有對應站台
        $siteToken = Input::header('sysToken');
        $promotion_m = new PromotionStationRepository();
        $code = $promotion_m->getCodeByToken($siteToken);
        if(empty($code)) {
            return ReturnFormatHelper::fail(ErrorCode::NO_THIS_SIDE, []);
        }
        Input::merge(['promotion_code' => $code['code']]);

        // 現在時間是否符合前台維護時間
        $maintain_res = $this->repository->check($code['code'], date('Y-m-d H:i:s'));
        if (! empty($maintain_res)) {
            $result['mt'] = '1';
            $result['mt_msg'] = $maintain_res['content'];
            return json_encode($result);
        } else {
            return $next($request);
        }
    }

}