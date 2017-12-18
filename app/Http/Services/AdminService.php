<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/10/23
 * Time: 下午 5:06
 */

namespace App\Http\Services;

use App\Http\Helper\ErrorCode;
use App\Http\Helper\LogHelper;
use App\Http\Repository\AdminRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class AdminService
{
    public $repository;

    public function __construct(AdminRepository $adminRepository)
    {
        $this->repository = $adminRepository;
    }

    /**
     * 登入邏輯
     * @param $account
     * @param $pwd
     * @return int
     */
    public function login($account, $pwd)
    {
        $this->repository->connectionMaster();
        $user = $this->repository->findByAccount($account);
        if ($user) {
            $check = Hash::check($pwd, $user->pwd);
            if ($check) {
                $token = Str_random(60);
                $api_token = Hash::make($token);
                Session::put($api_token, $user->account);
                $this->repository->loginSaveInfo($user);
                try {
                    $user->save();
                    $result['api_token'] = $api_token;
                    return $result;
                } catch (\Exception $e) {
                    Log::error(LogHelper::toFormatString('Unable Write'));
                    return ErrorCode::UNABLE_UPDATE;
                }
            }
            //password error
            Log::error(LogHelper::toFormatString('Password Error'));
            return ErrorCode::PASSWORD_ERROR;
        }
        //account error
        Log::error(LogHelper::toFormatString('Account Error'));
        return ErrorCode::ACCOUNT_ERROR;
    }
}