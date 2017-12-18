<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/10/24
 * Time: 上午 11:38
 */

namespace App\Http\Repository;

use App\Http\Controllers\InitController;
use App\Http\RepositoryProtocol\NewReportDetail;
use App\Http\Helper\LogHelper;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NewReportDetailRepository extends InitRepository implements RepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new NewReportDetail());
    }

    /**
     * 新增資料
     *
     * @param array $arr_input
     * @return boolean
     */
    public function insertData($arr_input)
    {
        try {
            $this->connectionMaster();
            $result = $this->model->insert(['new_report_id' => $arr_input['new_report_id'],
                'promotion_code' => $arr_input['promotion_code'],
                'mod_user' => $arr_input['mod_user'],
                'updated_at' => $arr_input['updated_at']
            ]);
        } catch (QueryException $e) {
            $result = FALSE;
            Log::error(LogHelper::toFormatString('Error Code : 56102. Message : ' . $e->getMessage()));
        }

        return $result;
    }

    /**
     * 根據 `new_report_id` 取出資料，只取 `promotion_code`
     *
     * @param int $int_reportID
     * @return array
     */
    public function getDataByReportID($int_reportID)
    {
        $result_data = array();

        try {
            $result_data = $this->model->select('promotion_code')
                ->where('new_report_id', $int_reportID)
                ->get()->toArray();
        } catch (QueryException $e) {
            Log::error(LogHelper::toFormatString('Error Code : 56104. Message : ' . $e->getMessage()));
        }

        return $result_data;
    }

    /**
     * 根據 `new_report_id` 還有 `promotion_code`(WHEREIN) ，刪除資料
     *
     * @param int $int_reportID
     * @param array $arr_pCode
     * @return null
     */
    public function delDataByReportIdAndPCode($int_reportID, $arr_pCode)
    {
        try {
            $this->connectionMaster();
            $result = $this->model->whereIn('promotion_code', $arr_pCode)
                ->where('new_report_id', $int_reportID)
                ->delete();
        } catch (QueryException $e) {
            Log::error(LogHelper::toFormatString('Error Code : 56104. Message : ' . $e->getMessage()));
            $result = FALSE;
        }

        return $result;
    }

    /**
     * 根據 `new_report_id` ，刪除資料
     *
     * @param int $int_reportID
     * @return null
     */
    public function delDataByReportID($int_reportID)
    {
        try {
            $this->connectionMaster();
            $result = $this->model->where('new_report_id', $int_reportID)
                ->delete();
        } catch (QueryException $e) {
            Log::error(LogHelper::toFormatString('Error Code : 56104. Message : ' . $e->getMessage()));
            $result = FALSE;
        }

        return $result;
    }
}