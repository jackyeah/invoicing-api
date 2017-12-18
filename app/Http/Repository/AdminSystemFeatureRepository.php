<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/11/10
 * Time: 上午 9:56
 */

namespace App\Http\Repository;


use App\Http\Helper\LogHelper;
use App\Http\Repository\Traits\TryCatchTrait;
use App\Http\RepositoryProtocol\AdminSystemFeature;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminSystemFeatureRepository extends InitRepository implements RepositoryInterface
{
    use TryCatchTrait;

    public function __construct()
    {
        parent::__construct(new AdminSystemFeature());
    }

    /**
     *
     *取得管理者kind code
     * @return array
     */
    public function userKindCode($user)
    {
        return $this->selectTryCatch(function () use ($user) {
            return $this->model->with(['systemFeatureKind' => function ($query) {
                $query->select(['id', 'kind_code']);
            }])->select(['user', 'system_feature_kind_id'])->where('user', $user)->get()->toArray();
        });
    }


    /**
     * 新增
     * @param $user
     * @param $systemFeatureKindId
     * @return bool
     */
    public function create($user, $systemFeatureKindId)
    {
        $this->connectionMaster();
        return $this->queryTryCatch(function () use ($user, $systemFeatureKindId) {
            $this->model->insert([
                'user' => $user,
                'system_feature_kind_id' => $systemFeatureKindId,
                'mod_user' => Auth::user()->account
            ]);
        });
    }

    /**
     * 刪除
     * @param $account
     * @return bool
     */
    public function deleteByAccount($account)
    {
        $this->connectionMaster();
        return $this->queryTryCatch(function () use ($account) {
            $selfAccount = Auth::user()->account;
            $this->model->where('user', $account)
                ->where('user', '<>', $selfAccount)
                ->where('user', '<>', 'admin')
                ->delete();
        });
    }

    public function deleteInArray($account, $list)
    {
        return $this->queryTryCatch(function () use ($account, $list) {
            $this->model->where('user', $account)->whereIn('system_feature_kind_id', $list)->delete();
        });
    }

    public function findFeatureByAccount($account)
    {
        return $this->selectTryCatch(function () use ($account) {
            return $this->model->select('system_feature_kind_id')
                ->where('user', $account)->get();
        });
    }

}