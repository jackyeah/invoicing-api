<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2018/1/20
 * Time: 下午 4:19
 */

namespace App\Http\Repository;

use App\Http\Repository\Traits\TryCatchTrait;
use App\Http\RepositoryProtocol\OrderSource;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Http\Helper\LogHelper;
use Illuminate\Support\Facades\Log;

class OrderSourceRepository extends InitRepository implements RepositoryInterface
{
    use TryCatchTrait;

    public function __construct()
    {
        parent::__construct(new OrderSource());
    }

    /**
     * 取得訂單來源
     * @return array|mixed
     */
    public function index()
    {
        return $this->selectTryCatch(function () {
            return $this->model->get()->toArray();
        });
    }

    /**
     * 新增訂單來源
     * @param $name
     * @return bool
     */
    public function create($name)
    {
        return $this->queryTryCatch(function () use ($name) {
            $this->model->name = $name;
            $this->model->save();
        });
    }

    /**
     * 更新訂單來源
     * @param $id
     * @param $name
     * @return bool
     */
    public function update($id, $name)
    {
        return $this->queryTryCatch(function () use ($id, $name) {
            $result = $this->model->find($id);
            $result->name = $name;
            $result->save();
        });
    }

    /**
     * 刪除訂單來源
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        return $this->queryTryCatch(function () use ($id) {
            $this->model->destroy($id);
        });
    }
}