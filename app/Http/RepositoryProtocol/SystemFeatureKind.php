<?php

namespace App\Http\RepositoryProtocol;


use Illuminate\Database\Eloquent\Model;

class SystemFeatureKind extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    //table
    protected $table = 'system_feature_kind';

    //默認有created_at  ,updated_at  欄位
    public $timestamps = false;

    //validate rules
    public static $searchRules = [

    ];

    //可批量賦值的欄位
    protected $fillable = [];

    //不可批量賦值的欄位
    protected $guarded = [];

    public function AdminSystemFeature()
    {
        return $this->hasMany(AdminSystemFeature::class, 'system_feature_kind_id', 'id');
    }

}