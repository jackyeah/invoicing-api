<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/10/11
 * Time: 下午 2:14
 */

namespace App\Http\Controllers;

use App\Http\Helper\AuthHelper;
use App\Http\Repository\AdminRepository;
use App\Http\Repository\AdminSystemFeatureRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;

class DemoController extends InitController
{
    public function index()
    {

        return;
    }


}