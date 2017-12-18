<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/11/24
 * Time: 下午 6:15
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\OperationsRecordTrait;
use App\Http\Helper\MaskAccountHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Http\Repository;
use App\Http\RepositoryProtocol;
use App\Http\Controllers\Traits\GetParamsTrait;

class RankingController extends InitController
{
    use GetParamsTrait;
    use OperationsRecordTrait;
    private $featureKindCode = 'ranking';

    public function __construct()
    {
        $this->middleware('feature:' . $this->featureKindCode, [
            'except' => [
                'getList_hot_FrontEnd',
                'getList_big_100_FrontEnd',
                'getList_big_200_FrontEnd',
                'getList_big_500_FrontEnd',
                'getList_big_1000_FrontEnd',
                'getList_fraction_FrontEnd',
                'getList_multiple_FrontEnd'
            ]]);
    }

    /**
     * 取得排行榜類型清單
     * @return array
     */
    public function getTypeList()
    {
        $repository = new Repository\RankingTypeRepository();

        return $this->success($repository->index());
    }

    /**
     * 取得排行榜清單
     * @return array
     */
    public function index()
    {
        // 驗證參數
        $validator = Validator::make(Input::all(), RepositoryProtocol\RankingType::$selectRules);
        if ($validator->fails()) {
            return $this->fail('59102');
        }

        $params = self::getParams(['type', 'promotionCode']);

        $repository_game = new Repository\RankingGameRepository();
        $repository_user = new Repository\RankingUserRepository();
        $repository_game_promo = new Repository\GamePromotionRepository();

        if ((int)$params['type'] < 6) {
            $result = $repository_game->index((int)$params['type'], $params['promotionCode']);
        } else {
            $result = $repository_user->index((int)$params['type'], $params['promotionCode']);
        }

        //MaskAccountHelper::maskUserID('user_id', $result);
        return $this->success($result);
    }

    /**
     * 編輯排行榜清單
     * @return array
     */
    public function update()
    {
        $params = self::getParams(['type', 'data_json']);
        $arr_data = json_decode($params['data_json'], TRUE);

        // 驗證參數
        $validator = Validator::make(Input::all(), RepositoryProtocol\RankingType::$rules);
        if ($validator->fails() || count($arr_data) != 10) {
            return $this->fail('59102');
        }

        $repository_game_Promo = new Repository\GamePromotionRepository();

        // 取得所有的遊戲清單id，驗證用
        $result_gameID = $repository_game_Promo->getAllID();
        $result_gameID_match = $repository_game_Promo->getPromoCode(array_unique(array_column($arr_data, 'game_promotion_id')));

        // 判斷傳入的 game_promotion_id 是否合法，判斷對應的 promotion_code 是否一致
        if (count(array_diff(array_column($arr_data, 'game_promotion_id'), array_column($result_gameID, 'id'))) > 0
            || count(array_unique(array_column($result_gameID_match, 'promotion_code'))) > 1
        ) {
            return $this->fail('59102');
        }

        // 將要新增的資料補齊
        foreach ($arr_data as $key => $arr_datum) {
            $arr_data[$key] = array_merge($arr_data[$key],
                ['type' => $params['type'], 'mod_user' => Auth::user()['account'], 'updated_at' => date('Y-m-d H:i:s')]);

        }

        // 根據 type ，選擇要異動的資料庫
        if ((int)$params['type'] < 6) {
            $repository_rank = new Repository\RankingGameRepository();
        } else {
            $repository_rank = new Repository\RankingUserRepository();
        }

        // 呼叫資料庫，刪除資料
        $result_delete = $repository_rank->delete($params['type'], array_unique(array_column($result_gameID_match, 'promotion_code'))[0]);

        if (! $result_delete) {
            $this->failRecord('delete', $repository_rank->getQueryLog());
            return $this->fail('56104');
        }
        $this->successRecord('delete', $repository_rank->getQueryLog());

        // 呼叫資料庫，新增資料
        $result_create = $repository_rank->create($arr_data);

        if ($result_create) {
            $this->successRecord('create', $repository_rank->getQueryLog());
            return $this->success();
        } else {
            $this->failRecord('create', $repository_rank->getQueryLog());
            return $this->fail('56102');
        }
    }

    /**
     * 刪除排行榜清單
     * @return array
     */
    public function delete()
    {
        // 驗證參數
        $validator = Validator::make(Input::all(), RepositoryProtocol\RankingType::$deleteRules);
        if ($validator->fails()) {
            return $this->fail('59102');
        }
        $params = self::getParams(['type', 'promotionCode']);

        // 根據 type ，選擇要異動的資料庫
        if ((int)$params['type'] < 6) {
            $repository_rank = new Repository\RankingGameRepository();
        } else {
            $repository_rank = new Repository\RankingUserRepository();
        }

        // 呼叫資料庫，刪除資料
        $result_delete = $repository_rank->delete($params['type'], $params['promotionCode']);

        if ($result_delete) {
            $this->successRecord('delete', $repository_rank->getQueryLog());
            return $this->success();
        } else {
            $this->failRecord('delete', $repository_rank->getQueryLog());
            return $this->fail('56104');
        }
    }

    /**
     * 前台 - 熱門遊戲排行
     * @return array
     */
    public function getList_hot_FrontEnd()
    {
        // 以推廣站代碼取得排行榜資料
        $repository = new Repository\RankingGameRepository();
        $result = $repository->index(1, Input::get('promotion_code'));

        return $this->success($result);
    }

    /**
     * 前台 - 遊戲大獎次數 - 100倍
     * @return array
     */
    public function getList_big_100_FrontEnd()
    {
        // 以推廣站代碼取得排行榜資料
        $repository = new Repository\RankingGameRepository();
        $result = $repository->index(2, Input::get('promotion_code'));

        return $this->success($result);
    }

    /**
     * 前台 - 遊戲大獎次數 - 200倍
     * @return array
     */
    public function getList_big_200_FrontEnd()
    {
        // 以推廣站代碼取得排行榜資料
        $repository = new Repository\RankingGameRepository();
        $result = $repository->index(3, Input::get('promotion_code'));

        return $this->success($result);
    }

    /**
     * 前台 - 遊戲大獎次數 - 500倍
     * @return array
     */
    public function getList_big_500_FrontEnd()
    {
        // 以推廣站代碼取得排行榜資料
        $repository = new Repository\RankingGameRepository();
        $result = $repository->index(4, Input::get('promotion_code'));

        return $this->success($result);
    }

    /**
     * 前台 - 遊戲大獎次數 - 1000倍
     * @return array
     */
    public function getList_big_1000_FrontEnd()
    {
        // 以推廣站代碼取得排行榜資料
        $repository = new Repository\RankingGameRepository();
        $result = $repository->index(5, Input::get('promotion_code'));

        return $this->success($result);
    }

    /**
     * 前台 - 玩家分數排行
     * @return array
     */
    public function getList_fraction_FrontEnd()
    {
        // 以推廣站代碼取得排行榜資料
        $repository = new Repository\RankingUserRepository();
        $result = $repository->index(6, Input::get('promotion_code'));

        return $this->success($result);
    }

    /**
     * 前台 - 玩家倍數排行
     * @return array
     */
    public function getList_multiple_FrontEnd()
    {
        // 以推廣站代碼取得排行榜資料
        $repository = new Repository\RankingUserRepository();
        $result = $repository->index(7, Input::get('promotion_code'));

        return $this->success($result);
    }
}