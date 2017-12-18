<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/11/14
 * Time: 下午 1:54
 */

namespace App\Http\Controllers;


use App\Http\Controllers\Traits\GetParamsTrait;
use App\Http\Controllers\Traits\OperationsRecordTrait;
use App\Http\Helper\ErrorCode;
use App\Http\Helper\FeatureHelper;
use App\Http\Helper\LogHelper;
use App\Http\RepositoryProtocol\AdminSystemFeature;
use App\Http\Services\SystemFeatureService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Repository\AdminRepository;
use App\Http\Helper\FormatHandleHelper;

class SystemFeatureController extends InitController
{
    use GetParamsTrait;
    use OperationsRecordTrait;
    private $featureKindCode = 'system_feature';
    protected $service;
    protected $featureHelper;

    public function __construct(SystemFeatureService $systemFeatureService, FeatureHelper $featureHelper)
    {
        $this->middleware('feature:' . $this->featureKindCode, ['except' => 'featureMenu']);
        $this->middleware('admin_level', ['except' => 'featureMenu']);
        $this->service = $systemFeatureService;
        $this->featureHelper = $featureHelper;
    }

    /**
     * get user's feature menu
     *
     * @return array
     */
    public function featureMenu()
    {
        return $this->success($this->featureHelper->getList(Auth::user()->account));
    }

    /**
     * 編輯管理者功能權限
     * @return array
     */
    public function edit()
    {
        $account = Input::get('user');
        $selfAccount = Auth::user()->account;
        //不能編輯自己的權限或是admin
        if ($account == $selfAccount || $account == 'admin') {
            Log::error(LogHelper::toFormatString('Can not edit this account'));
            return $this->fail(ErrorCode::CANT_EDIT_THIS_ACCOUNT);
        }
        //對input驗證
        $validator = Validator::make(Input::all(), AdminSystemFeature::$rules);
        //驗證失敗
        if ($validator->fails()) {
            Log::error(LogHelper::toFormatString('Input request params error'));
            return $this->fail(ErrorCode::VALIDATE_ERROR);
        }
        //取得權限input
        $featureList = (array)json_decode(Input::get('feature_list'), true);

        //檢查被編輯者的權限
        if (! $this->service->checkUserRights($account, $featureList)) {
            return $this->fail(ErrorCode::CANT_EDIT_THIS_ACCOUNT);
        };
        //新增和刪除名單
        $processList = $this->service->getProcess($account, $featureList);

        //建立清單
        if (! $this->service->create($account, $processList['createList'])) {
            //失敗操作紀錄
            $this->failRecord('create', $this->service->adminSystemFeatureRepository->getQueryLog());
            return $this->fail(ErrorCode::UNABLE_WRITE);
        }
        //成功操作紀錄
        $this->successRecord('create', $this->service->adminSystemFeatureRepository->getQueryLog());
        //刪除清單
        if (isset($processList['deleteList'])) {
            if (! $this->service->delete($account, $processList['deleteList'])) {
                $this->failRecord('delete', $this->service->adminSystemFeatureRepository->getQueryLog());
                return $this->fail(ErrorCode::DELETE_ERROR);
            }
            $this->successRecord('delete', $this->service->adminSystemFeatureRepository->getQueryLog());
        }
        //更新權限Redis
        $this->featureHelper->setFeatureList($account);

        return $this->success();
    }

    /**
     * 取得所有kind code 和 對應帳號權限
     * 過濾自己和admin資料
     * @return array|mixed
     */
    public function kindCodeStatus()
    {
        $result = $this->service->systemFeatureKindRepository->kindCodeStatus();
        return $this->success(FormatHandleHelper::delRelationIdForHasMany($result, 'admin_system_feature', 'system_feature_kind_id'));
    }

    /**
     * 取得所有kindcode
     * @return array
     */
    public function allKindCode()
    {
        if ($list = $this->service->systemFeatureKindRepository->getKindCode()) {
            return array_column($list, 'kind_code');
        }
        return [];
    }

    /**
     * @return array
     */
    public function userKindCode()
    {
        $user = Input::get('user');

        if ($list = $this->service->adminSystemFeatureRepository->userKindCode($user)) {
            $result = FormatHandleHelper::delRelationIdForBelongTo($list, 'system_feature_kind', 'id');
            return array_column($result, 'system_feature_kind');
        }
        return [];
    }

}