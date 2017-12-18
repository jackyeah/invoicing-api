<?php


namespace App\Http\Middleware;

use App\Http\Helper\ErrorCode;
use App\Http\Helper\FeatureHelper;
use App\Http\Helper\LogHelper;
use App\Http\Helper\ReturnFormatHelper;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FeatureMiddleware
{

    public function __construct()
    {

    }

    /**
     * @param $request
     * @param Closure $next
     * @param $featureKind
     * @return array|mixed
     */
    public function handle($request, Closure $next, $featureKind)
    {
        $user = Auth::user()->account;
        if ($user === 'admin') {
            return $next($request);
        }
        $list = (new FeatureHelper())->getList($user);
        if (in_array($featureKind, $list)) {
            return $next($request);
        }
        Log::error(LogHelper::toFormatString('Operation authority does not match'));
        return ReturnFormatHelper::fail(ErrorCode::OPERATION_LIMIT, []);

    }
}