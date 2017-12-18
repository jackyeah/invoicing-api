<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/10/11
 * Time: 下午 2:27
 */

namespace App\Http\Repository;

use App\Http\RepositoryProtocol\PromotionStation;
use App\Http\Helper\LogHelper;
use Illuminate\Support\Facades\Log;

class PromotionStationRepository extends InitRepository implements RepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new PromotionStation());
    }

    /**
     * 根據名稱, 狀態，取得推廣站台清單
     *
     * @param string $name
     * @param int $status
     * @return array
     */
    public function getAllList($name, $status)
    {
        $result_data = array();

        try {
            $result_conn = $this->model;
            $result_conn->orderBy('id');

            if ($name != '') {
                $result_conn->where('name', 'like', '%' . $name . '%');
            }

            $result_data = $result_conn->where('status', $status)->get();
        } catch (\Exception $e) {
            Log::error(LogHelper::toFormatString('Error Code : 56104'));
        }

        return $result_data;
    }

    /**
     * 根據url，取得推廣站台資料
     *
     * @param string $str_url
     * @param int $int_status
     * @return array
     */
    public function getDataByUrl($str_url, $int_status)
    {
        $result_data = array();

        try {
            $result_conn = $this->model;

            $result_data = $result_conn->select('code')->where('url', 'http://' . $str_url)
                ->where('status', $int_status)->get()->toArray();
        } catch (\Exception $e) {
            Log::error(LogHelper::toFormatString('Error Code : 56104'));
        }

        return $result_data;
    }

    /**
     * 根據 token，取得推廣站台代碼
     *
     * @param string $str_token
     * @return array
     */
    public function getCodeByToken($str_token)
    {
        $result_data = array();

        try {
            $result_data = $this->model->select('code')->where('token', $str_token)->first()->toArray();
        } catch (\Exception $e) {
            Log::error(LogHelper::toFormatString('Error Code : 56104'));
        }

        return $result_data;
    }

    /**
     * 根據code的陣列搜尋資料庫有多少個數
     *
     * @param $code_array
     * @return array
     */
    public function getCountByCodes($code_array)
    {
        $result_data = array();

        try {
            $result_data = $this->model->whereIn('code', $code_array)->count();
        } catch (\Exception $e) {
            Log::error(LogHelper::toFormatString('Error Code : 56104'));
        }

        return $result_data;
    }
}