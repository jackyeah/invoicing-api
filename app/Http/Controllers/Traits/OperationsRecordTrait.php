<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/12/7
 * Time: 下午 2:34
 */

namespace App\Http\Controllers\Traits;


use App\Http\Repository\OperationalRecordRepository;
use FastRoute\RouteCollector;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

trait OperationsRecordTrait
{
    /**
     * @param $action
     * @param $result
     * @return bool
     */
    public function record($action,$queryString, bool $result)
    {
        $operationalRecordRepository = new OperationalRecordRepository;

        if (! $feature = $this->featureKindCode) {
            return false;
        };
        //寫入紀錄
        if (! $operationalRecordRepository->create($feature, $action, json_encode($queryString), $result)) {
            return false;
        };
        return true;
    }

    public function failRecord($action,$queryString)
    {
        return $this->record($action,$queryString, false);
    }

    public function successRecord($action,$queryString)
    {
        return $this->record($action,$queryString, true);
    }
}