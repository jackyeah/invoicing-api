<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2018/1/30
 * Time: 下午 4:38
 */

namespace App\Http\Repository;

use App\Http\Repository\Traits\TryCatchTrait;
use App\Http\RepositoryProtocol\ShippingList;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Http\Helper\LogHelper;
use Illuminate\Support\Facades\Log;

class ShippingListRepository extends InitRepository implements RepositoryInterface
{
    use TryCatchTrait;

    public function __construct()
    {
        parent::__construct(new ShippingList());
    }

    /**
     * @param $date
     * @param $total_price
     * @param $profit
     * @param $order_source_id
     * @param $shipping_method_id
     * @return bool
     */
    public function create($date, $total_price, $profit, $order_source_id, $shipping_method_id)
    {
        $this->connectionMaster();

        try {
            $this->model->date = $date;
            $this->model->total_price = $total_price;
            $this->model->profit = $profit;
            $this->model->order_source_id = $order_source_id;
            $this->model->shipping_method_id = $shipping_method_id;
            $this->model->save();

            return $this->model->id;
        } catch (QueryException $e) {
            Log::error(LogHelper::toFormatString($e->getMessage()));
            return false;
        }
    }

    /**
     * 取得所有的庫存清單
     * @return array|mixed
     */
    public function index()
    {
        return $this->selectTryCatch(function () {
            return $this->model->with('shipping_record:id,shipping_list_id,product_style_id,quantity')
                ->join('order_source', 'shipping_list.order_source_id', '=', 'order_source.id')
                ->join('shipping_method', 'shipping_list.shipping_method_id', '=', 'shipping_method.id')
                ->select('shipping_list.id', 'shipping_list.total_price', 'shipping_list.profit', 'shipping_list.date',
                    DB::raw('order_source.name AS order_source_name'),
                    DB::raw('shipping_method.name AS shipping_method_name'))
                ->orderBy('shipping_list.id', 'ASC')
                ->get()->toArray();
        });
    }

    /**
     * 根據起始日期取出庫存清單
     * @param $startDate
     * @param $endDate
     * @return array|mixed
     */
    public function getListFixDate($startDate, $endDate)
    {
        return $this->selectTryCatch(function () use ($startDate, $endDate) {
            return $this->model->with('shipping_record:id,shipping_list_id,product_style_id,quantity')
                ->join('order_source', 'shipping_list.order_source_id', '=', 'order_source.id')
                ->join('shipping_method', 'shipping_list.shipping_method_id', '=', 'shipping_method.id')
                ->select('shipping_list.id', 'shipping_list.total_price', 'shipping_list.profit', 'shipping_list.date',
                    DB::raw('order_source.name AS order_source_name'),
                    DB::raw('shipping_method.name AS shipping_method_name'))
                ->whereBetween('date', [$startDate, $endDate])
                ->get()->toArray();
        });
    }

    /**
     * 取得單筆訂單資料
     * @param $id
     * @return array|mixed
     */
    public function detail($id)
    {
        return $this->selectTryCatch(function () use($id) {
            return $this->model->with('shipping_record:id,shipping_list_id,product_style_id,quantity')
                ->join('order_source', 'shipping_list.order_source_id', '=', 'order_source.id')
                ->join('shipping_method', 'shipping_list.shipping_method_id', '=', 'shipping_method.id')
                ->select('shipping_list.id', 'shipping_list.total_price', 'shipping_list.profit', 'shipping_list.date',
                    DB::raw('order_source.name AS order_source_name'),
                    DB::raw('order_source.id AS order_source_id'),
                    DB::raw('shipping_method.name AS shipping_method_name'),
                    DB::raw('shipping_method.id AS shipping_method_id'))
                ->where('shipping_list.id', $id)
                ->get()->toArray();
        });
    }
}