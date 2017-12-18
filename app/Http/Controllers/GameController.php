<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/11/23
 * Time: 上午 10:20
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\OperationsRecordTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Http\Repository;
use App\Http\Controllers\Traits\UploadFileTrait;
use App\Http\RepositoryProtocol\GameList;
use App\Http\Services\UploadService;
use App\Http\Controllers\Traits\GetParamsTrait;
use App\Http\Helper\PromotionHelper;
use App\Http\Helper\ErrorCode;
use App\Http\Helper\FormatHandleHelper;

class GameController extends InitController
{
    use UploadFileTrait;
    use GetParamsTrait;
    use OperationsRecordTrait;

    private $featureKindCode = 'game';
    protected $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
        $this->middleware('feature:' . $this->featureKindCode, ['except' => ['getList_FrontEnd']]);
    }

    /**
     * 取得遊戲清單
     * @return array
     */
    public function index()
    {
        // 驗證參數
        $validator = Validator::make(Input::all(), GameList::$search_rules);
        if ($validator->fails()) {
            return $this->fail('59102');
        }

        $repository = new Repository\GamePromotionRepository();

        $list = $repository->getGameList(Input::get('promotionCode'));
        return $this->success(FormatHandleHelper::delRelationIdForHasMany($list, 'game_list', 'id'));
    }

    /**
     * 新增遊戲資料
     * @return array
     */
    public function create()
    {
        // 驗證參數
        $validator = Validator::make(Input::all(), GameList::$rules);
        $pic = Input::get('pic');
        if ($validator->fails()) {
            return $this->fail('59102');
        }

        $promotion_code = Input::get('promotionCode');
        $promotion_helper = new PromotionHelper();
        if(! $promotion_helper->ValidatePromotionCode($promotion_code)){
            return $this->fail(ErrorCode::NO_THIS_SIDE);
        }

        $repository = new Repository\GameListRepository();
        $repository_game_pro = new Repository\GamePromotionRepository();

        // 將資料存入 `game_list`
        $result_id = $repository->create(Input::get('name'), $pic);

        if (!$result_id) {
            // 資料存入失敗，刪除上傳的圖片
            $this->deleteFile(config('define.img_path.game'), $pic);
            $this->failRecord('create', $repository->getQueryLog());
            return $this->fail('56102');
        }
        $this->successRecord('create', $repository->getQueryLog());
        // 將資料存入 `game_promotion`
        $insert_array = array();
        foreach (json_decode($promotion_code, true) as $value) {
            $insert_array[] = [
                'game_list_id' => $result_id,
                'promotion_code' => $value['pCode'],
                'mod_user' => Auth::user()['account']
            ];
        }

        if ($repository_game_pro->create($insert_array) == false) {
            $this->failRecord('create', $repository_game_pro->getQueryLog());
            return $this->fail('56102');
        }
        $this->successRecord('create', $repository_game_pro->getQueryLog());
        return $this->success();
    }

    /**
     * 編輯遊戲資料
     * @return array
     */
    public function update()
    {
        // 驗證參數
        $validator = Validator::make(Input::all(), GameList::$upd_rules);
        if ($validator->fails()) {
            return $this->fail('59102');
        }

        $params = self::getParams(['promotionCode', 'game_id', 'name', 'pic']);

        $promotion_helper = new PromotionHelper();
        if(! $promotion_helper->ValidatePromotionCode($params['promotionCode'])) {
            return $this->fail(ErrorCode::NO_THIS_SIDE);
        }

        $repository = new Repository\GameListRepository();
        $result_gameList = $repository->getDetails($params['game_id']);

        // 根據傳入的newsID，取得 `new_report_detail` 的資料
        $repository_game_promo = new Repository\GamePromotionRepository();
        $result_pCode = $repository_game_promo->getDataByGametID((int)$params['game_id']);

        // 比對是否有需要異動推廣站對應的部份
        $pCode_before = array_column($result_pCode, 'promotion_code');
        $pCode_after = array_column(json_decode($params['promotionCode'], TRUE), 'pCode');
        $pCode_diff_add = array_diff($pCode_after, $pCode_before);
        $pCode_diff_cut = array_diff($pCode_before, $pCode_after);

        // 更新資料
        $result_update = $repository->update($params['game_id'], $params['name'], $params['pic']);


        if (!$result_update) {
         $this->failRecord('update', $repository->getQueryLog());
            $this->deleteFile(config('define.img_path.game'), $params['pic']);
            return $this->fail('56102');
        }
        $this->successRecord('update', $repository->getQueryLog());
        if ($result_update['pic']) {
            $this->deleteFile(config('define.img_path.game'), $result_gameList['pic']);
        }

        // 新增 `game_promotion`
        if (count($pCode_diff_add) > 0) {
            $arr_add = array();
            foreach ($pCode_diff_add as $item) {
                $arr_add[] = ['game_list_id' => (int)$params['game_id'],
                    'promotion_code' => $item,
                    'mod_user' => Auth::user()['account']
                ];
            }
            $result_add = $repository_game_promo->create($arr_add);

            if (!$result_add) {
                $this->failRecord('create', $repository_game_promo->getQueryLog());
                return $this->fail('56102');
            }
            $this->successRecord('create', $repository_game_promo->getQueryLog());
        }

        // 刪除 `game_promotion`
        if (count($pCode_diff_cut) > 0) {
            $result_del = $repository_game_promo->delDataByGameIdAndPCode((int)$params['game_id'], $pCode_diff_cut);

            if (!$result_del) {
                $this->failRecord('delete', $repository_game_promo->getQueryLog());
                return $this->fail('56102');
            }
            $this->successRecord('delete', $repository_game_promo->getQueryLog());
        }

        return $this->success();
    }

    /**
     * 刪除遊戲資料
     * @return array
     */
    public function delete()
    {
        // 驗證參數
        $validator = Validator::make(Input::all(), GameList::$del_rules);
        if ($validator->fails()) {
            return $this->fail('59102');
        }

        $repository = new Repository\GameListRepository();
        $result_gameList = $repository->getDetails(Input::get('game_id'));

        $result_del = $repository->delete(Input::get('game_id'));

        $this->uploadService->deleteImg($result_gameList['pic'], config('define.img_path.game'));

        if ($result_del) {
            $this->successRecord('delete', $repository->getQueryLog());
            return $this->success();
        } else {
            $this->failRecord('delete', $repository->getQueryLog());
            return $this->fail('56104');
        }
    }

    /**
     * 前台，取得遊戲清單
     * @return array
     */
    public function getList_FrontEnd()
    {
        $repository = new Repository\GamePromotionRepository();
        $result = $repository->getGameList(Input::get('promotion_code'));

        return $this->success(FormatHandleHelper::delRelationIdForHasMany($result, 'game_list', 'id'));
    }
}