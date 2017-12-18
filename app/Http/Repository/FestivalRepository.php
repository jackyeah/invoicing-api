<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/12/5
 * Time: 下午 4:15
 */

namespace App\Http\Repository;

use App\Http\Repository\Traits\TryCatchTrait;
use App\Http\RepositoryProtocol\Festival;
use Illuminate\Support\Facades\Auth;

class FestivalRepository extends InitRepository implements RepositoryInterface
{
    use TryCatchTrait;

    public function __construct()
    {
        parent::__construct(new Festival());
    }

    /**
     * 取得各站活動清單
     * @return array|mixed
     */
    public function index()
    {
        return $this->selectTryCatch(function () {
            return $this->model->select('promotion_code', 'status')->get()->toArray();
        });
    }

    /**
     * 新增各站活動資料
     * @param $promotion_code
     * @param $status
     * @return array|mixed
     */
    public function create($promotion_code, $status)
    {
        $this->connectionMaster();

        return $this->queryTryCatch(function () use ($promotion_code, $status) {
            $this->model->promotion_code = $promotion_code;
            $this->model->status = $status;
            $this->model->save();
        });
    }

    /**
     * 更新各站活動資料
     * @param $promotion_code
     * @param $status
     * @return array|mixed
     */
    public function update($promotion_code, $status)
    {
        $this->connectionMaster();

        return $this->queryTryCatch(function () use ($promotion_code, $status) {
            $this->model->where('promotion_code', $promotion_code)
                ->update(['status' => $status, 'mod_user' => Auth::user()->account]);
        });
    }

    /**
     * 前台，取得活動狀態
     * @param $promotionCode
     * @return array|mixed
     */
    public function getData($promotionCode)
    {
        return $this->selectTryCatch(function () use ($promotionCode) {
            return $this->model->select('status')->where('promotion_code', $promotionCode)->get()->toArray();
        });
    }
}