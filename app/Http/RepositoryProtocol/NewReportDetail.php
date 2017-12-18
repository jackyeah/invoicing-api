<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/10/24
 * Time: 上午 11:37
 */

namespace App\Http\RepositoryProtocol;

use Illuminate\Database\Eloquent\Model;

class NewReportDetail extends Model
{
    public function __construct()
    {

    }

    protected $table = 'new_report_detail';

    public $timestamps = false;

    //validate rules
    public static $searchRules = [
        'promotionCode_f' => 'string|required'
    ];

    //可批量賦值的欄位
    protected $fillable = [];

    //不可批量賦值的欄位
    protected $guarded = [];


//    protected $dateFormat = '';
    public function newreport()
    {
        return $this->belongsTo('App\Http\RepositoryProtocol\NewReport', 'new_report_id');
    }

    public function promotion()
    {
        return $this->belongsTo('App\Http\RepositoryProtocol\PromotionStation', 'promotion_code', 'code');
    }
}