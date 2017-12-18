<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/10/23
 * Time: 下午 3:06
 */

namespace App\Http\Repository;

use App\Http\RepositoryProtocol\NewReportType;
use App\Http\Helper\LogHelper;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class NewReportTypeRepository extends InitRepository implements RepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new NewReportType());
    }

    /**
     * 根據名稱, 狀態，取得最新報導類別清單
     *
     * @return array
     */
    public function getAllList()
    {
        $result_data = array();
        try {
            $result_data = $this->model->select('id', 'type_name')->orderBy('id')->get()->toArray();
        } catch (QueryException $e) {
            Log::error(LogHelper::toFormatString('Error Code : 56104. Message : ' . $e->getMessage()));
        }

        return $result_data;
    }
}