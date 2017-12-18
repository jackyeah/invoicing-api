<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/10/18
 * Time: 下午 4:59
 */

namespace App\Http\Repository;


use App\Http\Helper\ErrorCode;
use App\Http\Helper\LogHelper;
use App\Http\Repository\Traits\TryCatchTrait;
use App\Http\RepositoryProtocol\Admin;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;

class AdminRepository extends InitRepository implements RepositoryInterface
{
    use TryCatchTrait;

    public function __construct()
    {
        parent::__construct(new Admin);
    }

    /**
     * @param $account
     * @return bool|mixed // false=repeat
     */
    public function checkAccountRepeat($account)
    {
        return $this->errorException(function () use ($account) {
            if ($this->model->where(['account' => $account])->exists()) {
                throw  new \ErrorException('account repeat');
            };
            return true;
        });
    }

    /**
     *
     * @param $account
     * @return array
     */
    public function findByAccount($account)
    {
        try {
            return $this->model->where(['account' => $account])->first();
        } catch (QueryException $e) {
            Log::error(LogHelper::toFormatString($e->getMessage()));
        }
        return [];

    }

    /**
     * 註冊管理者
     * @param $account
     * @param $pwd
     * @param $name
     * @param $email
     * @return bool
     */
    public function create($account, $pwd, $name, $email)
    {
        $this->connectionMaster();
        try {
            $this->model->account = $account;
            $this->model->pwd = Hash::make($pwd);
            $this->model->name = $name;
            $this->model->email = $email;
            $this->model->save();
            return true;
        } catch (QueryException $e) {
            Log::error(LogHelper::toFormatString('Unable Write'));
            return false;
        }
    }

    public function update($id, $upd_array)
    {
        $this->connectionMaster();
        try {
            $sql = $this->model->where('id', '=', $id);
            $sql->update($upd_array);
            return true;
        } catch (QueryException $e) {
            Log::error(LogHelper::toFormatString(ErrorCode::UNABLE_WRITE));
            return false;
        }
    }

    /**
     * @param $model
     */
    public function loginSaveInfo($model)
    {
        $model->login_ip = Input::ip();
        $model->login_dt = date('Y-m-d H:i:s');
    }

    /**
     * 清单
     * @param $account
     * @param $name
     * @param $status
     * @return array
     */
    public function getList($account, $name, $status)
    {
        try {
            $sql = $this->model->orderBy('id');

            if ($status != '') {
                if($status == '1') {
                    $sql->whereIn('status', [$status, 9]);
                } else {
                    $sql->where('status', '=', $status);
                }
            }

            if ($account != '') {
                $account_str = $account . '%';
                $sql->where('account', 'LIKE', $account_str);
            }

            if ($name != '') {
                $name_str = '%' . $name . '%';
                $sql->where('name', 'LIKE', $name_str);
            }


            return $sql->get()->toArray();
        } catch (QueryException $e) {
            Log::error(LogHelper::toFormatString(56104));
            return [];
        }
    }

    /**
     * Get Account List
     * Except admin
     * @return array|mixed
     */
    public function getAccountList()
    {
        return $this->selectTryCatch(function () {
            return $this->model->select('account')
                ->where('account', '<>', 'admin')
                ->orderBy('id', 'asc')
                ->get()->toArray();
        });
    }

    /**
     * @return array
     */
    public function getSystemFeature()
    {
        if ($id = Auth::user()->id) {
            try {
                return $this->model->find($id)->AdminSystemFeature()->select('user')->get()->toArray();
            } catch (QueryException $e) {
                Log::error(LogHelper::toFormatString($e->getMessage()));
            }
        }
        return [];

    }
}