<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2018/1/17
 * Time: 下午 5:55
 */

namespace App\Http\Repository;

use App\Http\Repository\Traits\TryCatchTrait;
use App\Http\RepositoryProtocol\PurchaseRecord;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Http\Helper\LogHelper;
use Illuminate\Support\Facades\Log;

class PurchaseRecordRepository extends InitRepository implements RepositoryInterface
{
    use TryCatchTrait;

    public function __construct()
    {
        parent::__construct(new PurchaseRecord());
    }

    public function create($insertData)
    {
        $this->connectionMaster();

        return $this->queryTryCatch(function () use ($insertData) {
            $this->model->insert($insertData);
        });
    }
}