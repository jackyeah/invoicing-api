<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/11/23
 * Time: 上午 11:19
 */

namespace App\Http\Repository;

use App\Http\Repository\Traits\TryCatchTrait;
use App\Http\RepositoryProtocol\GameList;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use App\Http\Helper\LogHelper;

class GameListRepository extends InitRepository implements RepositoryInterface
{
    use TryCatchTrait;

    public function __construct()
    {
        parent::__construct(new GameList());
    }

    public function find($id)
    {
        return $this->selectTryCatch(function () use ($id) {
            return $this->model->find($id);
        });
    }

    /**
     * 取得詳細資料
     *
     * @param $gameList_id
     * @return array
     */
    public function getDetails($gameList_id)
    {
        return $this->selectTryCatch(function () use ($gameList_id) {
            return $this->model->find($gameList_id)->toArray();
        });
    }

    /**
     * 新增遊戲資料
     * @param $name
     * @param $pic
     * @return array|mixed
     */
    public function create($name, $pic)
    {
        $this->connectionMaster();

        try {
            $this->model->name = $name;
            $this->model->pic = $pic;
            $this->model->save();
            return $this->model->id;
        } catch (QueryException $e) {
            Log::error(LogHelper::toFormatString($e->getMessage()));
        }
        return false;
    }

    /**
     * 編輯遊戲資料
     * @param $id
     * @param $name
     * @param $pic
     * @return array|mixed
     */
    public function update($id, $name, $pic)
    {
        $this->connectionMaster();

        try {
            $result = $this->model->find($id);
            $result->name = $name;
            $result->pic = $pic;
            $isDirty_pic = $result->isDirty('pic');
            $result->save();

            return ['pic' => $isDirty_pic];
        } catch (QueryException $e) {
            Log::error(LogHelper::toFormatString('Error Code : 56103. Message : ' . $e->getMessage()));
            return FALSE;
        }
    }

    /**
     * 編輯遊戲資料
     * @param $id
     * @return array|mixed
     */
    public function delete($id)
    {
        $this->connectionMaster();

        return $this->queryTryCatch(function () use ($id) {
            $this->model->destroy($id);
        });
    }
}