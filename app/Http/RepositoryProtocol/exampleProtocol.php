<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/10/18
 * Time: 下午 2:51
 */

namespace App\Http\RepositoryProtocol;


use Illuminate\Database\Eloquent\Model;

class exampleProtocol extends Model
{
    /*
     * event creating, created, updating, updated, saving,
     * saved,  deleting, deleted, restoring, restored
     * */
    public function __construct()
    {
        parent::__construct();
    }

    //table
    protected $table = 'admin_promotion_station';

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
    protected $guarded =[];


//    protected $dateFormat = '';

    protected static function boot()
    {
        self::saving(function () {

        });

        static::deleting(function () {

        });
        return true;
    }
}