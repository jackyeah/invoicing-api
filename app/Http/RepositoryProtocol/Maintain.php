<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/11/6
 * Time: 上午 10:57
 */

namespace App\Http\RepositoryProtocol;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Maintain extends Model
{

    protected $table = 'maintain';

    public $timestamps = false;

    protected $fillable = [
        'start_time', 'end_time', 'content', 'mod_user'
    ];

    public static $rules = [
        'promotion_code' => 'required|exists:promotion_station,code',
        'start_time' => 'required|date',
        'end_time' => 'required|date|after:start_time',
        'content' => 'string'
    ];

    public static $update_rule = [
        'promotionCode' => 'required',
        'start_time' => 'required|date',
        'end_time' => 'required|date|after:start_time',
        'content' => 'string'
    ];

    public static $delete_rule = [
        'id' => 'required|exists:maintain,id'
    ];

    public static $search_rule = [
        'id' => 'required|exists:maintain,id'
    ];
}