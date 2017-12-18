<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/12/7
 * Time: 下午 3:03
 */

namespace App\Http\Repository;


use App\Http\Repository\Traits\TryCatchTrait;
use App\Http\RepositoryProtocol\OperationalRecord;
use Illuminate\Support\Facades\Auth;

class OperationalRecordRepository extends InitRepository implements RepositoryInterface
{
    use TryCatchTrait;

    public function __construct()
    {
        parent::__construct(new OperationalRecord());
    }

    public function create($feature, $action, $queryString, $result)
    {
        return $this->queryTryCatch(function () use ($feature, $action, $queryString, $result) {
            $this->connectionMaster();
            $this->model->feature = $feature;
            $this->model->action = $action;
            $this->model->query_string = $queryString;
            $this->model->result = $result;
            return $this->model->save();
        });

    }

}