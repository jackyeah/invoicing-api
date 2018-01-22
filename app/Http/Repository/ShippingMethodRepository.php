<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2018/1/20
 * Time: 下午 4:00
 */

namespace App\Http\Repository;

use App\Http\Repository\Traits\TryCatchTrait;
use App\Http\RepositoryProtocol\ShippingMethod;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Http\Helper\LogHelper;
use Illuminate\Support\Facades\Log;

class ShippingMethodRepository extends InitRepository implements RepositoryInterface
{
    use TryCatchTrait;

    public function __construct()
    {
        parent::__construct(new ShippingMethod());
    }

    /**
     * 取得寄送方式
     * @return array|mixed
     */
    public function index()
    {
        return $this->selectTryCatch(function () {
            return $this->model->get()->toArray();
        });
    }

    /**
     * 新增寄送方式
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
     * 更新寄送方式
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
     * 刪除寄送方式
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