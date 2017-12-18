<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/10/16
 * Time: 下午 2:00
 */

namespace App\Providers;


use App\Http\RepositoryProtocol\Admin;
use App\Http\RepositoryProtocol\MultipleEssay;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\ServiceProvider;

class MultipleEssayProviders extends ServiceProvider
{
    public function boot()
    {
        MultipleEssay::creating(function ($multipleEssay) {
            //F1
            $multipleEssay->ownerid = '1';
            $multipleEssay->create_at = date('Y-m-d H:i:s');
            $multipleEssay->client_ip = Input::ip();
        });
        Admin::creating(function ($admin) {
            $admin->status = 1;
            $admin->updated_at = date('Y-m-d H:i:s');
        });
        return true;
    }
}