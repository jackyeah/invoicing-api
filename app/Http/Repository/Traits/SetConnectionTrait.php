<?php

namespace App\Http\Repository\Traits;


trait SetConnectionTrait
{
    public function connectionMaster()
    {
        $this->model->setConnection('master');
        $this->model->getConnection()->enableQueryLog();
    }
}