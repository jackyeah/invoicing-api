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

    /**
     * 取得庫存清單
     * @return array|mixed
     */
    public function index()
    {
        return $this->selectTryCatch(function () {
            return $this->model
                ->join('product_style', 'product.id', '=', 'product_style.product_id')
                ->select('product_style.id',DB::raw('product.id AS product_id'), 'product.name', 'product.coast', 'product.price'
                    , 'product_style.item_no', 'product_style.style', 'product_style.quality')
                ->orderBy('product_id', 'DESC')
                ->get()->toArray();
        });
    }

    /**
     * 取得安全庫存清單
     * @return array|mixed
     */
    public function safe()
    {
        return $this->selectTryCatch(function () {
            return $this->model
                ->join('product_style', 'product.id', '=', 'product_style.product_id')
                ->select('product_style.id', DB::raw('product.id AS product_id'), 'product.name'
                    , 'product_style.item_no', 'product_style.style', 'product_style.quality', 'product_style.safety_stock')
                ->orderBy('product.id', 'DESC')
                ->get()->toArray();
        });
    }

    /**
     * 根據 `id` ，更新成本
     * @param $id
     * @param $coast
     * @return bool
     */
    public function updateCoast($id, $coast)
    {
        return $this->queryTryCatch(function () use ($id, $coast) {
            $result = $this->model->find($id);
            $result->coast = $coast;
            $result->save();
        });
    }
}