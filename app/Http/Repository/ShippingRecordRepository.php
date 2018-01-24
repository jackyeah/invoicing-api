<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2018/1/24
 * Time: 下午 6:07
 */

namespace App\Http\Repository;

use App\Http\Repository\Traits\TryCatchTrait;
use App\Http\RepositoryProtocol\ShippingRecord;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Http\Helper\LogHelper;
use Illuminate\Support\Facades\Log;

class ShippingRecordRepository extends InitRepository implements RepositoryInterface
{
    use TryCatchTrait;

    public function __construct()
    {
        parent::__construct(new ShippingRecord());
    }

    /**
     * @param $shippingID
     * @param $source_id
     * @param $shipping_method_id
     * @param $product_style_id
     * @param $quantity
     * @param $price
     * @return bool
     */
    public function create($shippingID, $source_id, $shipping_method_id, $product_style_id, $quantity, $price)
    {
        $this->connectionMaster();

        return $this->queryTryCatch(function () use ($shippingID, $source_id, $shipping_method_id, $product_style_id, $quantity, $price) {
            $this->model->shippingID = $shippingID;
            $this->model->source_id = $source_id;
            $this->model->shipping_method_id = $shipping_method_id;
            $this->model->product_style_id = $product_style_id;
            $this->model->quantity = $quantity;
            $this->model->save();
        });
    }
}