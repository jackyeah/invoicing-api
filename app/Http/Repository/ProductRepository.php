<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2018/1/16
 * Time: 下午 4:40
 */

namespace App\Http\Repository;

use App\Http\Repository\Traits\TryCatchTrait;
use App\Http\RepositoryProtocol\Product;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Http\Helper\LogHelper;
use Illuminate\Support\Facades\Log;

class ProductRepository extends InitRepository implements RepositoryInterface
{
    use TryCatchTrait;

    public function __construct()
    {
        parent::__construct(new Product());
    }

    /**
     * 建立商品基本資料
     * @param $name
     * @param $purchase_date
     * @param $coast
     * @param $price
     * @return bool|mixed
     */
    public function create($name, $purchase_date, $coast, $price)
    {
        $this->connectionMaster();

        try {
            $this->model->name = $name;
            $this->model->purchase_date = $purchase_date;
            $this->model->coast = $coast;
            $this->model->price = $price;
            $this->model->save();

            return $this->model->id;
        } catch (QueryException $e) {
            Log::error(LogHelper::toFormatString($e->getMessage()));
            return false;
        }
    }
}