<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/11/27
 * Time: 上午 11:43
 */

namespace App\Http\Repository;

use App\Http\Repository\Traits\TryCatchTrait;
use App\Http\RepositoryProtocol\RankingUser;

class RankingUserRepository extends InitRepository implements RepositoryInterface
{
    use TryCatchTrait;

    public function __construct()
    {
        parent::__construct(new RankingUser());
    }

    /**
     * 取得排行榜清單
     * @param int $int_type
     * @param string $str_pCode
     * @return array|mixed
     */
    public function index($int_type, $str_pCode)
    {
        return $this->selectTryCatch(function () use ($int_type, $str_pCode) {
            return $this->model->select('game_promotion_id', 'data', 'user_id', 'stage_number')
                ->join('game_promotion', 'ranking_user.game_promotion_id', '=', 'game_promotion.id')
                ->where('game_promotion.promotion_code', $str_pCode)
                ->where('type', $int_type)
                ->get()->toArray();
        });
    }

    /**
     * 新增排行榜資料
     * @param array $arr_insert
     * @return array|mixed
     */
    public function create($arr_insert)
    {
        $this->connectionMaster();

        return $this->queryTryCatch(function () use ($arr_insert) {
            return $this->model->insert($arr_insert);
        });
    }

    /**
     * 刪除排行榜資料
     * @param int $int_typeID
     * @param string $str_promoCode
     * @return array|mixed
     */
    public function delete($int_typeID, $str_promoCode)
    {
        $this->connectionMaster();

        return $this->queryTryCatch(function () use ($int_typeID, $str_promoCode) {
            return $this->model->join('game_promotion', 'ranking_user.game_promotion_id', '=', 'game_promotion.id')
                ->where('game_promotion.promotion_code', $str_promoCode)
                ->where('type', $int_typeID)
                ->delete();
        });
    }

}