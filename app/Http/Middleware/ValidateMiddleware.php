<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/9/11
 * Time: 下午 3:39
 */

namespace App\Http\Middleware;

use App\Http\Helper\ErrorCode;
use App\Http\Helper\LogHelper;
use App\Http\Repository\PromotionStationRepository;
//use App\Http\Repository\SiteCodeRepository;
use App\Http\Helper\ReturnFormatHelper;
use App\Http\Helper\SessionHelper;
use Closure;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;


class ValidateMiddleware
{
    public function __construct()
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $sysToken = Input::header('sysToken');
        $period = Input::get('period');
        $limit = Input::get('limit');
//        $SiteCode = config('define.site_token.' . $sysToken);
        $ownerId = Input::get('ownerId');

        //token error
//        if (! $SiteCode) {
//            Log::error(LogHelper::toFormatString('Does not exist site token'));
//            return ReturnFormatHelper::fail(ErrorCode::NO_THIS_SIDE, []);
//        }

        if (! $period || ! $ownerId) {
            Log::error(LogHelper::toFormatString('Input request params error'));
            return ReturnFormatHelper::fail(ErrorCode::PARAMS_ERROR, []);
        }


        return $next($request);
    }
}