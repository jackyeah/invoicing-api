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
                ->orderBy('purchase_record.updated_at', 'ASC')->get()->toArray();
        });
    }

    /**
     * 建立進貨紀錄
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
}