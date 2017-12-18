<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/11/24
 * Time: 下午 5:48
 */

namespace App\Http\RepositoryProtocol;

use Illuminate\Database\Eloquent\Model;

class RankingType extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    protected $table = 'ranking_type';

    public $timestamps = false;

    public static $rules = [
        'type' => 'exists:ranking_type,id'
    ];

    public static $selectRules = [
        'type' => 'integer|exists:ranking_type,id',
        'promotionCode' => 'required|string'
    ];

    public static $deleteRules = [
        'type' => 'integer|exists:ranking_type,id',
        'promotionCode' => 'required|string'
    ];

}