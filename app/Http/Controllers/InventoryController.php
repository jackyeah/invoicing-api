<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2018/1/19
 * Time: 下午 3:21
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Http\Repository;
use App\Http\Controllers\Traits\GetParamsTrait;
use App\Http\Services\InventoryService;
use App\Http\Helper\ErrorCode;

class InventoryController extends InitController
{
    use GetParamsTrait;

    private $_service;

    function __construct()
    {
        $this->_service = new InventoryService();
    }

    /**
     * 取得庫存清單
     * @return array
     */
    public function index()
    {
        $repository_product = new Repository\ProductRepository();

        return $this->success($repository_product->index());
    }

    /**
     * 取得安全庫存清單
     * @return array
     */
    public function safe()
    {
        $repository_product = new Repository\ProductRepository();

        return $this->success(
            $this->_service->filterSafetyStock(Input::get('subtraction'), $repository_product->safe())
        );
    }
}