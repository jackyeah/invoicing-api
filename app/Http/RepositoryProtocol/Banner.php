<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/10/18
 * Time: 下午 2:51
 */

namespace App\Http\RepositoryProtocol;

use App\Http\RepositoryProtocol\Traits\RewriteTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Banner extends Model
{
    use RewriteTrait;

    public function __construct()
    {
        parent::__construct();
    }

    protected $table = 'banner';

    public $timestamps = false;

    protected $fillable = [
        'pic_web', 'pic_mobile', 'url', 'description', 'mod_user', 'status'
    ];

    public static $searchRules = [
        'banner_id' => 'required|int|exists:banner,id'
    ];

    public static $searchRules_front = [
        'promotionCode' => 'required|string'
    ];

    public static $rules = [
        'url' => 'string|min:3|max:200',
        'status' => 'required|boolean',
        'sort' => 'integer|min:0|max:9',
        'pic_web' => 'required|string',
        'pic_mobile' => 'string',
        'promotion_code' => 'required|json',
        'description' => 'string'
    ];

    public static $updateRules = [
        'id' => 'required|integer|exists:banner,id',
        'url' => 'string|min:3|max:200',
        'status' => 'required|boolean',
        'pic_web' => 'required|string',
        'pic_mobile' => 'string',
        'sort' => 'integer|min:0|max:9',
        'promotion_code' => 'required|json',
        'description' => 'string'
    ];

    public function banner_details()
    {
        return $this->hasMany('App\Http\RepositoryProtocol\BannerDetail', 'banner_id');
    }

    protected static function boot()
    {
        self::saving(function ($admin) {
            $admin->mod_user = Auth::user()->account;
        });

        static::deleting(function ($banner) {
            $banner->banner_details()->delete();
        });
        return true;
    }
}