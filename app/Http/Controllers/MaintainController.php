<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/11/6
 * Time: 上午 11:20
 */

namespace App\Http\Controllers;


use App\Http\Controllers\Traits\GetParamsTrait;
use App\Http\Controllers\Traits\OperationsRecordTrait;
use App\Http\Repository\MaintainRepository;
use App\Http\RepositoryProtocol\Maintain;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Http\Helper\ErrorCode;
use Illuminate\Validation\Rule;

class MaintainController extends InitController
{
    use GetParamsTrait;
    use OperationsRecordTrait;

    private $featureKindCode = 'maintain';
    public $repository;

    public function __construct()
    {
        $this->repository = new MaintainRepository();
        $this->middleware('feature:' . $this->featureKindCode, ['except' => []]);
    }

    /**
     * 取得維護清單
     * @return array
     */
    public function index()
    {
        return $this->success($this->repository->get());
    }

    /**
     * 新增站台維護資料
     * @return array
     */

    public function create()
    {
        $validator = Validator::make(Input::all(), Maintain::$rules);

        if ($validator->fails()) {
            return $this->fail(ErrorCode::VALIDATE_ERROR);
        }
        $params = self::getParams(['start_time', 'end_time', 'content', 'promotion_code']);

        if (!empty($this->repository->getByCode($params['promotion_code']))) {
            return $this->fail(ErrorCode::CONTENT_EXISTS);
        }

        if ($this->repository->create($params['start_time'], $params['end_time'], $params['content'],
                Auth::user()->account, $params['promotion_code']) === false
        ) {
            $this->failRecord('create', $this->repository->getQueryLog());
            return $this->fail(ErrorCode::UNABLE_WRITE);

        }
        $this->successRecord('create', $this->repository->getQueryLog());
        return $this->success();
    }

    /**
     * 更新站台維護資料
     * @return array
     */
    public function update()
    {
        // 驗證參數
        $validator = Validator::make(Input::all(), Maintain::$update_rule);
        if ($validator->fails()) {
            return $this->fail(ErrorCode::VALIDATE_ERROR);
        }

        $params = self::getParams(['promotionCode', 'start_time', 'end_time', 'content']);
        $arr_pCode = array_column(json_decode($params['promotionCode'], TRUE), 'pCode');

        // 判斷傳入的推廣站代碼，是否存在 `maintain`.`promotion_code`
        $result_count = $this->repository->checkPromotionStation($arr_pCode);
        if (count($arr_pCode) != $result_count || count($arr_pCode) < 1) {
            return $this->fail(ErrorCode::NONE_SITE);
        }

        $update = $this->repository->update($arr_pCode, $params['start_time'],
            $params['end_time'], $params['content'], Auth::user()['account']);

        if ($update == false) {
            $this->failRecord('update', $this->repository->getQueryLog());
            return $this->fail(ErrorCode::UNABLE_UPDATE);
        }
        $this->successRecord('update', $this->repository->getQueryLog());
        return $this->success();
    }

    /**
     * 刪除站台維護
     * @return array
     */
    public function delete()
    {
        $validator = Validator::make(Input::all(), Maintain::$delete_rule);

        if ($validator->fails()) {
            return $this->fail(ErrorCode::VALIDATE_ERROR);
        }

        if ($this->repository->delete(Input::get('id')) == false) {
            $this->failRecord('delete',$this->repository->getQueryLog());
            return $this->fail(ErrorCode::DELETE_ERROR);
        }
        $this->successRecord('delete',$this->repository->getQueryLog());
        return $this->success();
    }
}