<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/10/30
 * Time: 上午 11:22
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\GetParamsTrait;
use App\Http\Controllers\Traits\OperationsRecordTrait;
use App\Http\Controllers\Traits\UploadFileTrait;
use App\Http\Helper\ErrorCode;
use App\Http\RepositoryProtocol\Banner;
use App\Http\Services\BannerService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Http\Helper\PromotionHelper;
use App\Http\Helper\FormatHandleHelper;

class BannerController extends InitController
{
    use UploadFileTrait;
    use GetParamsTrait;
    use OperationsRecordTrait;

    protected $service;
    private $featureKindCode = 'banner';

    public function __construct(BannerService $bannerService)
    {
        $this->service = $bannerService;
        $this->middleware('feature:' . $this->featureKindCode, ['except' => ['getBannerList_FrontEnd']]);
    }

    /**
     * 取得所有banner的清單
     *
     * @return array
     */
    public function index()
    {
        $status = Input::get('status');

        $banner_list = $this->service->repository->getAllList($status);
        return $this->success(FormatHandleHelper::returnPromotionForWith($banner_list, 'banner_details'));
    }

    /**
     * 取得banner的詳細資料
     *
     * @param banner_id
     *
     * @return array
     */
    public function detail()
    {
        // input banner id
        $validator = Validator::make(Input::all(), Banner::$searchRules);

        if ($validator->fails()) {
            return $this->fail('59102');
        }

        $banner_detail = (array)$this->service->repository->getDetails(Input::get('banner_id'));
        return $this->success(FormatHandleHelper::returnPromotionOneForWith($banner_detail, 'banner_details'));
    }

    /**create banner
     * @return array
     */
    public function create()
    {
        // 驗證參數
        $params = self::getParams(['url', 'status', 'sort', 'description', 'promotion_code', 'pic_web', 'pic_mobile']);
        if (! $this->service->validateRules()) {
            return $this->fail(ErrorCode::VALIDATE_ERROR);
        };

        $promotion_helper = new PromotionHelper();
        if(! $promotion_helper->ValidatePromotionCode($params['promotion_code'])){
            return $this->fail(ErrorCode::NO_THIS_SIDE);
        }

        // 新增資料 `banner`
        if (! $id = $this->service->create($params['url'], $params['description'], $params['status'], $params['pic_web'], $params['pic_mobile'], $params['sort'])) {
            if ($params['pic_web']) {
                $this->deleteFile(config('define.img_path.pic_web'), $params['pic_web']);
            }
            if ($params['pic_mobile']) {
                $this->deleteFile(config('define.img_path.pic_mobile'), $params['pic_mobile']);
            }
            $this->failRecord('create', $this->service->repository->getQueryLog());
            return $this->fail(ErrorCode::UNABLE_WRITE);
        };

        $this->successRecord('create', $this->service->repository->getQueryLog());
        // 新增資料 `banner_detail`
        $insertArray = $this->service->toInsertArray($id, json_decode($params['promotion_code'], true));
        if (! $this->service->detailRepository->insertDetail($insertArray)) {
            $this->failRecord('create', $this->service->detailRepository->getQueryLog());
            return $this->fail(ErrorCode::UNABLE_WRITE);
        }
        $this->successRecord('create', $this->service->detailRepository->getQueryLog());
        return $this->success();
    }

    /**
     * 更新banner
     * @return array
     */
    public function update()
    {
        // 驗證參數
        if (! $this->service->validateUpdateRules()) {
            return $this->fail(ErrorCode::VALIDATE_ERROR);
        };
        $params = self::getParams(['id', 'url', 'status', 'sort', 'description', 'promotion_code', 'pic_web', 'pic_mobile']);

        $promotion_helper = new PromotionHelper();
        if(! $promotion_helper->ValidatePromotionCode($params['promotion_code'])){
            return $this->fail(ErrorCode::NO_THIS_SIDE);
        }
        
        // 取得所需更新的資料
        $banner_detail = $this->service->repository->getDetails($params['id']);

        // 判斷對應的推廣站代碼有無更新
        $result_promotion_update = $this->service->checkPromotionUpdate($banner_detail['banner_details'],
            $params['promotion_code'], $params['id'], Auth::user()->account);
        if (! $result_promotion_update) {
            $this->failRecord('update', $this->service->detailRepository->getQueryLog());
            return $this->fail(ErrorCode::UNABLE_UPDATE);
        }
        $this->successRecord('update', $this->service->detailRepository->getQueryLog());

        // 更新 `banner`
        $result_banner_update = $this->service->update($params);
        if (! $result_banner_update) {
            $this->failRecord('update', $this->service->repository->getQueryLog());
            return $this->fail(ErrorCode::UNABLE_UPDATE);
        }
        $this->successRecord('update', $this->service->repository->getQueryLog());

        // 判斷圖片是否有更新，若更新，則須刪掉舊的圖片
        if ($result_banner_update['pic_web']) {
            $this->deleteFile(config('define.img_path.pic_web'), $banner_detail['pic_web']);
        }
        if ($result_banner_update['pic_mobile']) {
            $this->deleteFile(config('define.img_path.pic_mobile'), $banner_detail['pic_mobile']);
        }

        return $this->success();
    }

    /**
     * 刪除banner包含圖片檔
     * @return array
     */
    public function delete()
    {
        // 驗證參數
        $validator = Validator::make(Input::all(), Banner::$searchRules);
        if ($validator->fails()) {
            return $this->fail('59102');
        }

        $params = self::getParams(['banner_id']);
        $banner_detail = $this->service->repository->getDetails($params['banner_id']);

        // 刪除 `banner` 的資料
        if ($this->service->repository->delete($params['banner_id']) === false) {
            $this->failRecord('delete', $this->service->repository->getQueryLog());
            return $this->fail('56103');
        }
        $this->successRecord('delete', $this->service->repository->getQueryLog());

        // 刪除對應的圖片
        $this->service->deleteImg($banner_detail['pic_web'], config('define.img_path.web'));
        $this->service->deleteImg($banner_detail['pic_mobile'], config('define.img_path.mobile'));

        return $this->success();
    }

    /**
     * 取得所有banner的清單，前台
     *
     * @return array
     */
    public function getBannerList_FrontEnd()
    {
        $banner_list = $this->service->repository->getListByPCode(Input::get('promotion_code'));

        return $this->success($banner_list);
    }
}