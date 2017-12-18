<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/11/16
 * Time: 下午 4:23
 */

namespace App\Http\Repository;


use App\Http\Repository\Traits\TryCatchTrait;
use App\Http\RepositoryProtocol\SystemFeatureKind;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Support\Facades\Auth;

class SystemFeatureKindRepository extends InitRepository implements RepositoryInterface
{
    use TryCatchTrait;

    public function __construct()
    {
        parent::__construct(new SystemFeatureKind());
    }

    /**
     * 取得所有kind code
     * @return array|mixed
     */
    public function getKindCode()
    {
        return $this->selectTryCatch(function () {
            return $this->model->select('kind_code')->get()->toArray();
        });
    }

    public function kindCodeStatus()
    {
        return $this->selectTryCatch(function () {
            return $this->model->with(['AdminSystemFeature' => function (HasOneOrMany $query) {
                $selfAccount = Auth::user()->account;
                $query->select('system_feature_kind_id', 'user')
                    ->where('user', '<>', $selfAccount)
                    ->where('user', '<>', 'admin');
            }])->select('id', 'kind_code')->get()->toArray();
        });
    }

    /**
     * 取得某kind_code的id
     * @return array|mixed
     */
    public function getIdByKindCode($code)
    {
        return $this->selectTryCatch(function () use ($code) {
            return $this->model->select('id')->where('kind_code', $code)->first()->toArray();
        });
    }
}