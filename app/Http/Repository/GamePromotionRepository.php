<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/11/28
 * Time: 上午 11:54
 */

namespace App\Http\Repository;

use App\Http\Repository\Traits\TryCatchTrait;
use App\Http\RepositoryProtocol\GamePromotion;

class GamePromotionRepository extends InitRepository implements RepositoryInterface
{
    use TryCatchTrait;

    public function __construct()
    {
        parent::__construct(new GamePromotion());
    }

    /**
     * 取得所有遊戲ID
     * @return array|mixed
     */
    public function getAllID()
    {
        return $this->selectTryCatch(function () {
            return $this->model->select('id', 'promotion_code')->get()->toArray();
        });
    }

    /**
     * 取得所有遊戲ID
     * @param array $arr_GameProID
     * @return array|mixed
     */
    public function getPromoCode($arr_GameProID)
    {
        return $this->selectTryCatch(function () use ($arr_GameProID) {
            return $this->model->select('promotion_code')->whereIn('id', $arr_GameProID)->get()->toArray();
        });
    }

    /**
     * 取得遊戲清單
     * @param int $int_pCode
     * @return array|mixed
     */
    public function getGameList($int_pCode)
    {
        return $this->selectTryCatch(function () use ($int_pCode) {
            return $this->model->select('id', 'game_list_id', 'promotion_code')->with('game_list:id,name,pic')->where('promotion_code', $int_pCode)->get()->toArray();
        });
    }

    /**
     * 根據 `game_list_id` 取出資料，只取 `promotion_code`
     *
     * @param int $int_gameID
     * @return array
     */
    public function getDataByGametID($int_gameID)
    {
        return $this->selectTryCatch(function () use ($int_gameID) {
            return $this->model->select('promotion_code')->where('game_list_id', $int_gameID)->get()->toArray();
        });
    }

    /**
     * 新增遊戲與推廣站對應資料
     * @param array $arr_data
     * @return array|mixed
     */
    public function create($arr_data)
    {
        $this->connectionMaster();

        return $this->queryTryCatch(function () use ($arr_data) {
            $this->model->insert($arr_data);
        });
    }

    /**
     * 根據 `game_list_id` 還有 `promotion_code`(WHEREIN) ，刪除資料
     *
     * @param int $int_gameID
     * @param array $arr_pCode
     * @return null
     */
    public function delDataByGameIdAndPCode($int_gameID, $arr_pCode)
    {
        $this->connectionMaster();

        return $this->queryTryCatch(function () use ($int_gameID, $arr_pCode) {
            $this->model->whereIn('promotion_code', $arr_pCode)
                ->where('game_list_id', $int_gameID)
                ->delete();
        });
    }
}