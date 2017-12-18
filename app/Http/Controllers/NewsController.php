<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/10/30
 * Time: 上午 9:59
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\OperationsRecordTrait;
use App\Http\Controllers\Traits\UploadFileTrait;
use App\Http\Repository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Http\RepositoryProtocol;
use App\Http\Services\NewsReportService;
use App\Http\Helper\PromotionHelper;
use App\Http\Controllers\Traits\GetParamsTrait;
use App\Http\Helper\ErrorCode;
use App\Http\Helper\FormatHandleHelper;

class NewsController extends InitController
{
    use GetParamsTrait;
    use UploadFileTrait;
    use OperationsRecordTrait;
    private $featureKindCode = 'news';

    public function __construct()
    {
        $this->middleware('feature:' . $this->featureKindCode, [
            'except' => ['getNewsList_FrontEnd', 'getNewsContent_FrontEnd'
            ]]);
    }

    /**
     * 取得最新報導'類別'清單
     *
     * @return array
     */
    public function getNewsTypeList()
    {
        // 取得最新報導類別清單
        $repository = new Repository\NewReportTypeRepository();
        $result_data = $repository->getAllList();

        return $this->success($result_data);
    }

    /**
     * 取得最新報導清單
     *
     * @return array
     */
    public function index()
    {
        // 驗證參數
        $validator = Validator::make(Input::all(), RepositoryProtocol\NewReport::$searchRules);
        if ($validator->fails()) {
            return $this->fail(ErrorCode::VALIDATE_ERROR);
        }

        $repository = new Repository\NewReportRepository();
        $result_data = $repository->getListByParameters(Input::all());

        return $this->success(FormatHandleHelper::returnPromotionForJoin($result_data));
    }

    /**
     * 取得單筆最新報導詳細資料
     *
     * @return array
     */
    public function getNewsContent()
    {
        $id = Input::get('newsID');
        if ($id == '') {
            return $this->fail(ErrorCode::VALIDATE_ERROR);
        }

        $repository = new Repository\NewReportRepository();
        $result_data = $repository->getContentByID($id);

        return $this->success(FormatHandleHelper::returnPromotionForWith($result_data, 'news_details'));
    }

    /**
     * 新增最新報導
     *
     * @return array
     */
    public function create()
    {
        // 驗證參數
        $validator = Validator::make(Input::all(), RepositoryProtocol\NewReport::$Rules);
        if ($validator->fails()) {
            return $this->fail(ErrorCode::VALIDATE_ERROR);
        }

        $promotion_code = Input::get('promotionCode');
        $promotion_helper = new PromotionHelper();
        if(! $promotion_helper->ValidatePromotionCode($promotion_code)){
            return $this->fail(ErrorCode::NO_THIS_SIDE);
        }

        $repository = new Repository\NewReportRepository();
        $result_addNews_id = $repository->insertData(Input::all());

        // 確認新增回傳的資料
        if (! $result_addNews_id) {
            $this->failRecord('create', $repository->getQueryLog());
            $this->deleteFile(config('define.img_path.news'), Input::get('pic'));
            return $this->fail('56102');
        }
        $this->successRecord('create', $repository->getQueryLog());

        $repository_detail = new Repository\NewReportDetailRepository();
        foreach (json_decode($promotion_code, TRUE) as $item) {
            $arr_insert = ['new_report_id' => $result_addNews_id,
                'promotion_code' => $item['pCode'],
                'mod_user' => 'admin',
                'updated_at' => date('Y-m-d H:i:s', time())];

            $repository_detail->insertData($arr_insert);
            $this->successRecord('create', $repository_detail->getQueryLog());
        }

        return $this->success(['news_id' => $result_addNews_id]);
    }

    /**
     * 編輯最新報導
     *
     * @return array
     */
    public function update()
    {
        // 驗證參數
        $validator = Validator::make(Input::all(), RepositoryProtocol\NewReport::$updateRules);
        if ($validator->fails()) {
            return $this->fail(ErrorCode::VALIDATE_ERROR);
        }

        $params = self::getParams(['newsID', 'promotionCode', 'date', 'newsTypeID', 'title', 'overview', 'status', 'content', 'pic']);

        $promotion_helper = new PromotionHelper();
        if(! $promotion_helper->ValidatePromotionCode($params['promotionCode'])){
            return $this->fail(ErrorCode::NO_THIS_SIDE);
        }

        // 根據傳入的newsID，取得 `new_report_detail` 的資料
        $repository_report_detail = new Repository\NewReportDetailRepository();
        $repository = new Repository\NewReportRepository();

        // 根據傳入的newsID，取得 `new_report_detail` 的資料
        $result_pCode = $repository_report_detail->getDataByReportID((int)$params['newsID']);

        // 比對是否有需要異動
        $pCode_before = array_column($result_pCode, 'promotion_code');
        $pCode_after = array_column(json_decode($params['promotionCode'], TRUE), 'pCode');
        $pCode_diff_add = array_diff($pCode_after, $pCode_before);
        $pCode_diff_cut = array_diff($pCode_before, $pCode_after);

        // 取得修改前的圖片檔名
        $oldName = $repository->getImgById($params['newsID']);
        $ImgPath = config('define.img_path.news');

        // 根據傳入的newsID，更改 `new_report` 的資料
        $result_update = $repository->update($params);

        // 確認編輯回傳的資料
        if (! $result_update) {
            $this->deleteFile($ImgPath, $params['pic']);
            $this->failRecord('update', $repository->getQueryLog());
            return $this->fail('56103');
        }
        $this->successRecord('update', $repository->getQueryLog());

        // 判斷是否有異動圖片
        if($result_update['pic']){
            // 刪除圖片
            $this->deleteFile($ImgPath, $oldName['pic']);
        }

        // 新增 `new_report_detail` 的資料
        if (count($pCode_diff_add) > 0) {
            foreach ($pCode_diff_add as $item) {
                $arr_add = ['new_report_id' => (int)$params['newsID'],
                    'promotion_code' => $item,
                    'mod_user' => Auth::user()['account'],
                    'updated_at' => date('Y-m-d H:i:s', time())
                ];
                $repository_report_detail->insertData($arr_add);
            }
        }

        // 刪除 `new_report_detail` 的資料
        if (count($pCode_diff_cut) > 0) {
            $repository_report_detail->delDataByReportIdAndPCode((int)$params['newsID'], $pCode_diff_cut);
        }

        return $this->success([]);
    }

    /**
     * 刪除最新報導
     *
     * @return array
     */
    public function delete()
    {
        // 驗證參數
        $validator = Validator::make(Input::all(), RepositoryProtocol\NewReport::$deleteRules);
        if ($validator->fails()) {
            return $this->fail(ErrorCode::VALIDATE_ERROR);
        }

        $news_id = Input::get('newsID');

        // 根據 `id` 刪除 `new_report` 的資料
        $repository_report = new Repository\NewReportRepository();
        $pic = $repository_report->getImgById($news_id);
        $result_del = $repository_report->delDataByID($news_id);

        if ($result_del) {
            //將圖片也一併刪除
            if (! empty($pic)) {
                $new_service = new NewsReportService();
                $new_service->deleteImg($pic['pic'], config('define.img_path.news'));
            }
            $this->successRecord('delete', $repository_report->getQueryLog());
            return $this->success();
        } else {
            $this->failRecord('delete', $repository_report->getQueryLog());
            return $this->fail('56104');
        }
    }

    /**
     * 取得最新報導清單，前台
     *
     * @return array
     */
    public function getNewsList_FrontEnd()
    {
        // 以推廣站代碼取得最新報導資料
        $repository = new Repository\NewReportRepository();
        $result_data = $repository->getList_FrontEnd(Input::get('promotion_code'));

        return $this->success($result_data);
    }

    /**
     * 取得單筆最新報導詳細資料，前台
     *
     * @return array
     */
    public function getNewsContent_FrontEnd()
    {
        // 以推廣站代碼, 報導id，取得最新報導資料
        $repository = new Repository\NewReportRepository();
        $result_data = $repository->getContent_FrontEnd(Input::get('promotion_code'), Input::get('newsID'));

        return $this->success($result_data);
    }
}