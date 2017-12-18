<?php

namespace App\Http\RepositoryProtocol;

use App\Http\RepositoryProtocol\Traits\RewriteTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class NewReport extends Model
{
    use RewriteTrait;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'news_time' => 'required',
            'title' => 'required',
            'content' => 'required',
        ];
    }

    //listener events
    protected static function boot()
    {

        static::creating(function ($model) {
            $model->mod_user = Auth::user()['account'];
            $model->updated_at = date('Y-m-d H:i:s');
        });
        static::updating(function ($newReport) {
            $newReport->updated_at = date('Y-m-d H:i:s');
            $newReport->mod_user = Auth::user()['account'];
        });
        static::deleting(function ($newReport) {
            $newReport->news_details()->delete();
        });
    }

    protected $table = 'new_report';

    protected $connection = 'master';

    public $timestamps = false;

    //可批量賦值的欄位
    protected $fillable = ['type_id', 'title', 'content'];

    //validate rules
    public static $searchRules = [
        'promotionCode' => 'string',
        'newsTypeID' => 'int|exists:new_report_type,id',
        'title' => 'string',
        'status' => 'int',
        's_Date' => 'date',
        'e_Date' => 'date',
        'adminAccount' => 'string'
    ];

    public static $updateRules = [
        'promotionCode' => 'required|json',
        'date' => 'required|date',
        'newsTypeID' => 'required|int|exists:new_report_type,id',
        'title' => 'required|string',
        'overview' => 'string',
        'status' => 'required|int|in:0,1',
        'content' => 'required|string',
        'newsID' => 'required|int|exists:new_report,id',
        'pic' => 'required|string'
    ];

    public static $Rules = [
        'promotionCode' => 'required|json',
        'date' => 'required|date',
        'newsTypeID' => 'required|int|exists:new_report_type,id',
        'title' => 'required|string',
        'overview' => 'string',
        'status' => 'required|int|in:0,1',
        'content' => 'required|string',
        'pic' => 'required|string'
    ];

    public static $deleteRules = [
        'newsID' => 'required|int|exists:new_report,id'
    ];


    //不可批量賦值的欄位
    protected $guarded = [];

    protected $hidden = [''];


    public function news_details()
    {
        return $this->hasMany('App\Http\RepositoryProtocol\NewReportDetail', 'new_report_id');
    }
}