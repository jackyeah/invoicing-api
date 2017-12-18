<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/11/24
 * Time: 下午 5:50
 */

namespace App\Http\Repository;

use App\Http\Repository\Traits\TryCatchTrait;
use App\Http\RepositoryProtocol\RankingType;
use Illuminate\Database\QueryException;

class RankingTypeRepository extends InitRepository implements RepositoryInterface
{
    use TryCatchTrait;

    public function __construct()
    {
        parent::__construct(new RankingType());
    }

    /**
     * 取得遊戲清單
     * @return array|mixed
     */
    public function index()
    {
        return $this->selectTryCatch(function () {
            return $this->model->get()->toArray();
        });
    }

}