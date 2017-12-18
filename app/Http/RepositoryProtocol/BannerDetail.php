<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/10/25
 * Time: 下午 4:21
 */

namespace App\Http\RepositoryProtocol;


use Illuminate\Database\Eloquent\Model;

class BannerDetail extends Model
{
    public function __construct()
    {

    }

    protected $table = 'banner_detail';

    public function banner()
    {
        return $this->belongsTo('App\Http\RepositoryProtocol\Banner');
    }

    public function promotion()
    {
        return $this->belongsTo('App\Http\RepositoryProtocol\PromotionStation', 'promotion_code', 'code');
    }
}