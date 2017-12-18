<?php

namespace App\Http\Repository\Traits;

use App\Http\Helper\LogHelper;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

trait TryCatchTrait
{
    public function queryTryCatch(callable $callback)
    {
        try {
            call_user_func($callback);
            return true;
        } catch (QueryException $e) {
            Log::error(LogHelper::toFormatString($e->getMessage()));
        }
        return false;
    }

    public function selectTryCatch(callable $callback)
    {
        try {
            return call_user_func($callback);
        } catch (QueryException $e) {
            Log::error(LogHelper::toFormatString($e->getMessage()));
        }
        return [];
    }

    public function errorException(callable $callback)
    {
        try {
            return call_user_func($callback);
        } catch (\ErrorException $e) {
            Log::error(LogHelper::toFormatString($e->getMessage()));
        }
        return false;
    }
}