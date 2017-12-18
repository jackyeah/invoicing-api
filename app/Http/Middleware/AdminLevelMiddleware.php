<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/11/29
 * Time: 下午 5:18
 */

namespace App\Http\Middleware;

use App\Http\Helper\ErrorCode;
use App\Http\Helper\LogHelper;
use App\Http\Helper\ReturnFormatHelper;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminLevelMiddleware
{
    public function __construct()
    {

    }

    /**
     * 最高權限限制
     * @param $request
     * @param Closure $next
     * @return array|mixed
     */
    public function handle($request, Closure $next)
    {
        $status = Auth::user()->status;
        if ($status == '9') {
            return $next($request);
        }
        Log::error(LogHelper::toFormatString('Admin stats is not 9'));
        return ReturnFormatHelper::fail(ErrorCode::OPERATION_LIMIT, []);

    }
}