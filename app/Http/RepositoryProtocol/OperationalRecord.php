<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/12/7
 * Time: 下午 2:53
 */

namespace App\Http\RepositoryProtocol;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class OperationalRecord extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    //table
    protected $table = 'operational_record';

    //默認有created_at  ,updated_at  欄位
    public $timestamps = false;

    //validate rules
    public static $searchRules = [
//        'game_list_lang'=>'string|in:en,cn,tw',
//        'game_code'=>'string|in:GP,MG,CP',
//        'game_name'=>'string',
//        'default'=>'boolean',
    ];

    //可批量賦值的欄位
    protected $fillable = [];

    //不可批量賦值的欄位
    protected $guarded = [];


//    protected $dateFormat = '';

    protected static function boot()
    {
        self::creating(function ($operationalRecord) {
            $operationalRecord->user = Auth::user()->account;
            $operationalRecord->ip = Input::ip();
        });

        return true;
    }
}