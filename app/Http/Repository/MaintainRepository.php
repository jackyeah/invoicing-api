<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/11/6
 * Time: 上午 10:58
 */

namespace App\Http\Repository;

use App\Http\Repository\Traits\TryCatchTrait;
use App\Http\RepositoryProtocol\Maintain;
use Illuminate\Database\QueryException;

class MaintainRepository extends InitRepository implements RepositoryInterface
{
    use TryCatchTrait;

    public function __construct()
    {
        parent::__construct(new Maintain());
    }

    /**
     * 取得站台維修資料
     * @return array
     */
    public function get()
    {
        try {
            return $this->model->get()->toArray();
        } catch (QueryException $e) {
            return [];
        }
    }

    /**
     * 根據推廣站代碼，取得資料
     * @param $code
     * @return array
     */
    public function getByCode($code)
    {
        try {
            return $this->model->where('promotion_code', $code)->get()->toArray();
        } catch (QueryException $e) {
            return [];
        }
    }

    /**
     * 新增維護資料
     * @param $start_time
     * @param $end_time
     * @param $content
     * @param $mod_user
     * @param $p_code
     * @return bool
     */
    public function create($start_time, $end_time, $content, $mod_user, $p_code)
    {
        try {
            $this->connectionMaster();
            $this->model->start_time = $start_time;
            $this->model->end_time = $end_time;
            $this->model->content = $content;
            $this->model->mod_user = $mod_user;
            $this->model->promotion_code = $p_code;
            $this->model->save();
            return true;
        } catch (QueryException $e) {
            return false;
        }
    }

    /**
     * 更新站台維護資料
     * @param $arr_pCode
     * @param $start_time
     * @param $end_time
     * @param $content
     * @param $mod_user
     * @return bool
     */
    public function update($arr_pCode, $start_time, $end_time, $content, $mod_user)
    {
        try {
            $this->connectionMaster();
            $this->model->whereIn('promotion_code', $arr_pCode)
                ->update([
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'content' => $content,
                    'mod_user' => $mod_user
                ]);
            return true;
        } catch (QueryException $e) {
            return false;
        }
    }

    /**
     * 確認是否這時間是否有在此站台維護時間內
     * @param $code
     * @param $time
     * @return array
     */
    public function check($code, $time)
    {
        try {
            return $this->model->where('promotion_code', $code)
                                ->where('start_time', '<=', $time)
                                ->where('end_time', '>=', $time)
                                ->select('content')
                                ->first()->toArray();
        } catch (QueryException $e) {
            return [];
        }
    }

    /**
     * 刪除維護資料
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        try {
            $this->connectionMaster();
            $this->model->destroy($id);
            return true;
        } catch (QueryException $e) {
            return false;
        }
    }

    /**
     * 確認推廣站代碼是否存在
     * @param $arr_pCode
     * @return array|mixed
     */
    public function checkPromotionStation($arr_pCode)
    {
        return $this->selectTryCatch(function () use($arr_pCode) {
            return $this->model->whereIn('promotion_code', $arr_pCode)->count();
        });
    }
}