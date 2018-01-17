<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2018/1/17
 * Time: 上午 10:58
 */

namespace App\Http\Repository;

use App\Http\Repository\Traits\TryCatchTrait;
use App\Http\RepositoryProtocol\ProductStyle;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Http\Helper\LogHelper;
use Illuminate\Support\Facades\Log;

class ProductStyleRepository extends InitRepository implements RepositoryInterface
{
    use TryCatchTrait;

    public function __construct()
    {
        parent::__construct(new ProductStyle());
    }

    public function create($arr_insert)
    {
        $this->connectionMaster();

        return $this->queryTryCatch(function () use ($arr_insert) {
            return $this->model->insert($arr_insert);
        });

    }
}