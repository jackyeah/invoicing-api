<?php
/**
 * Created by PhpStorm.
 * User: frogyeh
 * Date: 2017/12/26
 * Time: 下午9:47
 */

namespace App\Http\Repository;

use App\Http\Repository\Traits\TryCatchTrait;
use App\Http\RepositoryProtocol\Manufacturers;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use App\Http\Helper\LogHelper;


class ManufacturersRepository extends InitRepository implements RepositoryInterface
{
    use TryCatchTrait;

    public function __construct()
    {
        parent::__construct(new Manufacturers());
    }

    /**
     * 取得廠商清單
     * @return array|mixed
     */
    public function index()
    {
        return $this->selectTryCatch(function () {
            return $this->model->select('id', 'name')->get()->toArray();
        });
    }

}