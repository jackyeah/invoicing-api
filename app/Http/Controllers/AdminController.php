<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/10/18
 * Time: 下午 5:35
 */

namespace App\Http\Controllers;


use App\Http\Controllers\Traits\GetParamsTrait;
use App\Http\Controllers\Traits\OperationsRecordTrait;
use App\Http\Helper\ErrorCode;
use App\Http\Helper\FeatureHelper;
use App\Http\Helper\LogHelper;
use App\Http\Repository\AdminRepository;
use App\Http\RepositoryProtocol\Admin;
use App\Http\Services\AdminService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\In;


class AdminController extends InitController
{
    use GetParamsTrait;
    use OperationsRecordTrait;

    public $service;
    private $featureKindCode = 'admin';

    public function __construct(AdminService $adminService)
    {
        $this->service = $adminService;
        $this->middleware('feature:' . $this->featureKindCode, ['except' => ['login']]);
        $this->middleware('admin_level', ['only' => ['register', 'index']]);
    }

    /**
     * 管理者登入
     * @return array
     */
    public function login()
    {
        $account = Input::get('account');
        $pwd = Input::get('pwd');
        if ($account && $pwd) {
            $result = $this->service->login($account, $pwd);
            if (isset($result['api_token'])) {
                /*$helper = new FeatureHelper();
                if (! $helper->getList($account)) {
                    $helper->setFeatureList($account);
                }*/
                return $this->success($result['api_token']);
            }
            return $this->fail($result);
        }
        Log::error(LogHelper::toFormatString('Input request params error'));
        return $this->fail(ErrorCode::PARAMS_ERROR);
    }

    /**
     * 註冊管理者
     * @return array
     */
    public function register()
    {
        //取得request value
        $params = self::getParams(['account', 'pwd', 'pwd_confirmation', 'name', 'email']);

        //更改驗證account欄位 unique驗證方法的錯誤訊息
        $messages = [
            'account.unique' => ErrorCode::ACCOUNT_EXIST,
        ];

        //驗證request 是否符合admin rules
        $validator = Validator::make(Input::all(), Admin::rules(), $messages);

        //判斷是否有帳號重複
        if(in_array(ErrorCode::ACCOUNT_EXIST, $validator->errors()->get('account')))
        {
            return $this->fail(ErrorCode::ACCOUNT_EXIST);
        }

        if ($validator->fails()) {
            Log::error(LogHelper::toFormatString('Input request params error'));
            return $this->fail(ErrorCode::VALIDATE_ERROR);
        }
        //註冊資料寫入DB
        $result = $this->service->repository->create($params['account'], $params['pwd'], $params['name'], $params['email']);
        if ($result) {
            return $this->success();
        }
        Log::error(LogHelper::toFormatString('Unable write of database'));
        return $this->fail(ErrorCode::UNABLE_WRITE);

    }

    /**
     * 使用者列表
     * @return array
     */
    public function index()
    {
        $status = Input::get('status');
        $account = Input::get('account');
        $name = Input::get('name');
        $validator = Validator::make(['status' => $status], Admin::searchRules());

        if ($validator->fails()) {
            Log::error(LogHelper::toFormatString('Input request params error'));
            return $this->fail(ErrorCode::VALIDATE_ERROR);
        }

        $result = $this->service->repository->getList($account, $name, $status);

        return $this->success($result);
    }


}