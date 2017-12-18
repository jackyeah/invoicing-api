<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/11/10
 * Time: 上午 9:26
 */

namespace App\Http\RepositoryProtocol;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AdminSystemFeature extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public static function boot()
    {
        self::saving(function ($model) {
            $model->mod_user = Auth::user()->account;
        });
    }

    public static $rules = [
        'user' => 'required|string|exists:admin,account',
        'feature_list' => 'required',
    ];

    //table
    protected $table = 'admin_system_feature';

    //默認有created_at  ,updated_at  欄位
    public $timestamps = false;

    //validate rules
    public static $searchRules = [

    ];

    //可批量賦值的欄位
    protected $fillable = ['user', 'system_feature_kind_id','mod_user'];

    //不可批量賦值的欄位
    protected $guarded = [];

    public function systemFeatureKind()
    {
        return $this->belongsTo(SystemFeatureKind::class, 'system_feature_kind_id');
    }
}