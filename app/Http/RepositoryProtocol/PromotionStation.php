<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/10/11
 * Time: 下午 2:22
 */

namespace App\Http\RepositoryProtocol;


use Illuminate\Database\Eloquent\Model;

class PromotionStation extends Model
{
    public function __construct()
    {

    }

    protected $table = 'promotion_station';

    public static $searchRules = [
        'name'=>'string',
        'status' => 'required|int|in:0,1',
        'url' => 'string'
    ];
}