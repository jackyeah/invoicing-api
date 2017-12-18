<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/12/5
 * Time: 下午 4:17
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Http\Repository;
use App\Http\RepositoryProtocol;
use App\Http\Controllers\Traits\GetParamsTrait;

class FestivalController extends InitController
{
    use GetParamsTrait;
    private $featureKindCode = 'festival';

    public function __construct()
    {
        $this->middleware('feature:' . $this->featureKindCode, ['except' => ['getList_FrontEnd']]);
    }

    /**
     * 取得活動清單
     * @return array
     */
    public function index()
    {
        $repository = new Repository\FestivalRepository();

        return $this->success($repository->index());
    }

    /**
     * 更新活動狀態
     * @return array
     */
    public function update()
    {
        $params = self::getParams(['status', 'promotionCode']);

        // 驗證參數
        $validator = Validator::make($params, RepositoryProtocol\Festival::$rules);
        if ($validator->fails()) {
            return $this->fail('59102');
        }

        $repository = new Repository\FestivalRepository();

        // 判斷該站是否有活動的資料
        $validator = Validator::make($params, RepositoryProtocol\Festival::$check_rules);

        if ($validator->fails()) {
            // 新增
            $result = $repository->create($params['promotionCode'], $params['status']);
        } else {
            // 更新
            $result = $repository->update($params['promotionCode'], $params['status']);
        }

        if ($result) {
            return $this->success();
        } else {
            return $this->fail('56102');
        }
    }

    /**
     * 前台，取得活動狀態
     * @return array
     */
    public function getList_FrontEnd()
    {
        $repository = new Repository\FestivalRepository();
        $result = $repository->getData(Input::get('promotion_code'));

        return $this->success($result);
    }
}