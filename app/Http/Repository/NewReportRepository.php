<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/10/23
 * Time: 下午 6:19
 */

namespace App\Http\Repository;

use App\Http\RepositoryProtocol\NewReport;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Http\Helper\LogHelper;
use Illuminate\Support\Facades\Log;

class NewReportRepository extends InitRepository implements RepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new NewReport());
    }

    /**
     * 根據 `id` ，更新圖片資料
     *
     * @param int $id
     * @param string $name
     * @return null
     */
    public function updatePic($id, $name)
    {
        $result = TRUE;
        $this->connectionMaster();
        $conn = $this->model->find($id);

        if (! $conn) {
            $result = FALSE;
        }

        try {
            $conn->pic = $name;
            $conn->save();
        } catch (QueryException $e) {
            Log::error(LogHelper::toFormatString('Error Code : 56103. Message : ' . $e->getMessage()));
            $result = FALSE;
        }
        return $result;
    }

    /**
     * 根據搜尋條件 ，取出清單
     *
     * @param array $arr_input
     * @return null
     */
    public function getListByParameters($arr_input)
    {
        $result_data = array();

        try {
            $result_conn = $this->model;

            $result = $result_conn->join('new_report_detail', 'new_report.id', '=', 'new_report_detail.new_report_id')
                ->join('promotion_station', 'new_report_detail.promotion_code', '=', 'promotion_station.code')
                ->select('new_report.id', 'new_report.news_time', 'new_report.title', 'new_report.mod_user',
                    'new_report.status', 'new_report.type_id', DB::raw('group_concat(promotion_station.code) AS pCode'))
                ->groupBy('new_report.id');

            if ($arr_input['newsTypeID'] != '') {
                $result->where('new_report.type_id', $arr_input['newsTypeID']);
            }

            if ($arr_input['promotionCode'] != '') {
                $result->where('new_report_detail.promotion_code', $arr_input['promotionCode']);
            }

            if ($arr_input['s_Date'] != '' && $arr_input['e_Date'] != '') {
                $result->whereBetween('new_report.news_time', [$arr_input['s_Date'], $arr_input['e_Date']]);
            }

            if ($arr_input['adminAccount'] != '') {
                $result->where('new_report.mod_user', $arr_input['adminAccount']);
            }

            if ($arr_input['title'] != '') {
                $result->where('new_report.title', 'LIKE', '%' . $arr_input['title'] . '%');
            }

            if ($arr_input['status'] != '') {
                $result->where('new_report.status', $arr_input['status']);
            }

            $result->orderBy('new_report.news_time', 'DESC');
            $result->orderBy('new_report_detail.promotion_code');

            $result_data = $result->get()->toArray();
        } catch (QueryException $e) {
            Log::error(LogHelper::toFormatString('Error Code : 56104. Message : ' . $e->getMessage()));
        }

        return $result_data;
    }

    /**
     * 根據 `id` ，取出資料
     *
     * @param int $int_newsID
     * @return null
     */
    public function getContentByID($int_newsID)
    {
        $result_data = array();

        try {
            $result_conn = $this->model;

            $result_data = $result_conn->with('news_details:new_report_id,promotion_code')
                ->where('id', $int_newsID)->select('id', 'type_id', 'news_time', 'title', 'overview', 'content', 'pic',
                    'status', DB::raw("'" . config('define')['img_server']['news'] . "' as domain"))
                ->get()->toArray();
        } catch (QueryException $e) {
            Log::error(LogHelper::toFormatString('Error Code : 56104. Message : ' . $e->getMessage()));
        }

        return $result_data;
    }

    /**
     * 新增資料
     *
     * @param array $arr_input
     * @return null
     */
    public function insertData($arr_input)
    {
        try {
            $this->connectionMaster();
            $this->model->type_id = $arr_input['newsTypeID'];
            $this->model->news_time = $arr_input['date'];
            $this->model->title = $arr_input['title'];
            $this->model->overview = $arr_input['overview'];
            $this->model->content = $arr_input['content'];
            $this->model->status = $arr_input['status'];
            $this->model->pic = $arr_input['pic'];

            if ($this->model->save()) {
                $result = $this->model->id;
            } else {
                $result = FALSE;
            }
        } catch (QueryException $e) {
            Log::error(LogHelper::toFormatString('Error Code : 56102. Message : ' . $e->getMessage()));

            $result = FALSE;
        }

        return $result;
    }

    /**
     * 根據 `id` ，編輯資料
     *
     * @param array $arr_input
     * @return mixed
     */
    public function update($arr_input)
    {
        try {
            $this->connectionMaster();

            $result = $this->model->find($arr_input['newsID']);
            $result->type_id = $arr_input['newsTypeID'];
            $result->news_time = $arr_input['date'];
            $result->title = $arr_input['title'];
            $result->overview = $arr_input['overview'];
            $result->content = $arr_input['content'];
            $result->status = $arr_input['status'];
            $result->pic = $arr_input['pic'];
            $isDirty_pic = $result->isDirty('pic');
            $result->save();

            return ['pic' => $isDirty_pic];
        } catch (QueryException $e) {
            Log::error(LogHelper::toFormatString('Error Code : 56103. Message : ' . $e->getMessage()));
            return FALSE;
        }
    }

    /**
     * 根據 `id` ，刪除資料
     *
     * @param int $int_reportID
     * @return null
     */
    public function delDataByID($int_reportID)
    {
        try {
            $this->connectionMaster();
            $result = $this->model->destroy($int_reportID);
        } catch (QueryException $e) {
            Log::error(LogHelper::toFormatString('Error Code : 56104. Message : ' . $e->getMessage()));
            $result = FALSE;
        }

        return $result;
    }

    /**
     * 取出前台所需清單
     *
     * @param string $str_promotionCode
     * @return null
     */
    public function getList_FrontEnd($str_promotionCode)
    {
        $result_data = array();

        try {
            $result_conn = $this->model;

            $result_data = $result_conn->join('new_report_detail', 'new_report.id', '=', 'new_report_detail.new_report_id')
                ->select('new_report.id', 'new_report.news_time', 'new_report.title', 'new_report.pic',
                    'new_report.overview', 'new_report.type_id',
                    DB::raw("'" . config('define')['img_server']['news'] . "' as domain"))
                ->where('new_report_detail.promotion_code', $str_promotionCode)
                ->where('new_report.status', 1)
                ->orderBy('new_report.news_time', 'ASC')->get()->toArray();
        } catch (QueryException $e) {
            Log::error(LogHelper::toFormatString('Error Code : 56104. Message : ' . $e->getMessage()));
        }

        return $result_data;
    }

    /**
     * 取出前台所需的詳細資料
     *
     * @param string $str_promotionCode
     * @param string $str_newsID
     * @return null
     */
    public function getContent_FrontEnd($str_promotionCode, $str_newsID)
    {
        $result_data = array();

        try {
            $result_conn = $this->model;

            $result_data = $result_conn->join('new_report_detail', 'new_report.id', '=', 'new_report_detail.new_report_id')
                ->select('new_report.news_time', 'new_report.title', 'new_report.type_id', 'new_report.content',
                    'new_report.pic', DB::raw("'" . config('define')['img_server']['news'] . "' as domain"))
                ->where('new_report_detail.promotion_code', $str_promotionCode)
                ->where('new_report.id', $str_newsID)
                ->where('new_report.status', 1)
                ->groupBy('new_report.id')->get()->toArray();
        } catch (QueryException $e) {
            Log::error(LogHelper::toFormatString('Error Code : 56104. Message : ' . $e->getMessage()));
        }

        return $result_data;
    }

    public function getImgById($news_id)
    {
        $result_data = array();
        try {
            $result_data = $this->model->select('pic')->where('id', $news_id)->first()->toArray();
        } catch (QueryException $e) {
            Log::error(LogHelper::toFormatString('Error Code : 56104. Message : ' . $e->getMessage()));
        }

        return $result_data;
    }
}