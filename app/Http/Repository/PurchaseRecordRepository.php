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

    /**
     * 取出資料
     * @return array|mixed
     */
    public function index()
    {
        return $this->selectTryCatch(function () {
            return $this->model
                ->join('product_style', 'purchase_record.product_style_id', '=', 'product_style.id')
                ->join('product', 'product_style.product_id', '=', 'product.id')
                ->select(DB::raw('purchase_record.id AS purchase_id'), 'product.name', 'product_style.item_no', 'purchase_record.quantity',
                    DB::raw('product_style.quality AS totalQuantity'), 'product_style.style', 'product.coast', 'purchase_record.purchase_time')
                ->orderBy('purchase_record.updated_at', 'DESC')->get()->toArray();
        });
    }

    /**
     * 建立進貨紀錄，多筆
     * @param $insertData
     * @return bool
     */
    public function create($insertData)
    {
        $this->connectionMaster();

        return $this->queryTryCatch(function () use ($insertData) {
            $this->model->insert($insertData);
        });
    }

    /**
     * 建立進貨紀錄，單筆
     * @param $product_style_id
     * @param $quantity
     * @param $purchase_time
     * @param $account
     * @return bool
     */
    public function create_single($product_style_id, $quantity, $purchase_time, $account)
    {
        $this->connectionMaster();

        return $this->queryTryCatch(function () use ($product_style_id, $quantity, $purchase_time, $account) {
            $this->model->product_style_id = $product_style_id;
            $this->model->quantity = $quantity;
            $this->model->purchase_time = $purchase_time;
            $this->model->mod_user = $account;
            $this->model->save();
        });
    }

    /**
     * 根據 `id` ，取得數量 `quantity`, `product_style_id`
     * @param $id
     * @return array|mixed
     */
    public function getQuantityData($id)
    {
        return $this->selectTryCatch(function () use ($id) {
            return $this->model->select('product_style_id', 'quantity')->find($id)->toArray();
        });
    }

    /**
     * 更新進貨紀錄的數量, 日期
     * @param $id
     * @param $quantity
     * @param $date
     * @return bool
     */
    public function update($id, $quantity, $date)
    {
        $this->connectionMaster();

        return $this->queryTryCatch(function () use ($id, $quantity, $date) {
            $result = $this->model->find($id);

            $result->quantity = $quantity;
            $result->purchase_time = $date;
            $result->save();
        });
    }

    public function delete($id)
    {
        $this->connectionMaster();

        return $this->queryTryCatch(function () use ($id) {
            $this->model->destroy($id);
        });
    }
}