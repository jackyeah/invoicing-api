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

    /**
     * 建立商品樣式資料
     * @param $arr_insert
     * @return bool
     */
    public function create($arr_insert)
    {
        $this->connectionMaster();

        return $this->queryTryCatch(function () use ($arr_insert) {
            return $this->model->insert($arr_insert);
        });
    }

    /**
     * 根據產品id，取得資料
     * @param $product_id
     * @return array|mixed
     */
    public function getData($product_id)
    {
        return $this->selectTryCatch(function () use ($product_id) {
            return $this->model->select('id', 'quality', 'updated_at')->where('product_id', $product_id)->get()->toArray();
        });
    }
}