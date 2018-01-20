<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2018/1/20
 * Time: 下午 4:00
 */

namespace App\Http\Repository;

use App\Http\Repository\Traits\TryCatchTrait;
use App\Http\RepositoryProtocol\ShippingMethod;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Http\Helper\LogHelper;
use Illuminate\Support\Facades\Log;

class ShippingMethodRepository extends InitRepository implements RepositoryInterface
{
    use TryCatchTrait;

    public function __construct()
    {
        parent::__construct(new ShippingMethod());
    }

    /**
     * 取得寄送方式
     * @return array|mixed
     */
    public function index()
    {
        return $this->selectTryCatch(function () {
            return $this->model->get()->toArray();
        });
    }



}