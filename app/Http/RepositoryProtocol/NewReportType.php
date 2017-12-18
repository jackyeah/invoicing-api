<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/10/23
 * Time: 下午 3:07
 */

namespace App\Http\RepositoryProtocol;

use Illuminate\Database\Eloquent\Model;

class NewReportType extends Model
{
    public function __construct()
    {

    }

    protected $table = 'new_report_type';

    protected $connection = 'master';

    public $timestamps = false;

    //validate rules
    public static $searchRules = [
        'type_name'=>'string',
        'status' => 'int'
    ];

    //可批量賦值的欄位
    protected $fillable = [];

    //不可批量賦值的欄位
    protected $guarded =[];


//    protected $dateFormat = '';

}