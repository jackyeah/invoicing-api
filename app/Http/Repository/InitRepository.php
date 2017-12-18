<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/11/1
 * Time: 下午 5:42
 */

namespace App\Http\Repository;


use App\Http\Repository\Traits\SetConnectionTrait;
use Illuminate\Database\Eloquent\Model;

class InitRepository
{
    use SetConnectionTrait;

    protected $model = null;

    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->model->getConnection()->enableQueryLog();
    }

    public function getQueryLog()
    {
        return $this->model->getConnection()->getQueryLog();
    }
}