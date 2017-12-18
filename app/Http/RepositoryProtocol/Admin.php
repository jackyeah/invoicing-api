<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/10/18
 * Time: 下午 2:50
 */

namespace App\Http\RepositoryProtocol;

use Illuminate\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class Admin extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    public function __construct()
    {
        parent::__construct();
    }

    public static function boot()
    {
        Admin::saving(function ($admin) {
            $admin->updated_at = date('Y-m-d H:i:s');
            $admin->mod_user = Auth::user()['account'];
        });
        Admin::creating(function ($admin) {
            $admin->status = 1;
        });
        return true;
    }

    protected $table = 'admin';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'pwd',
        'api_token',
    ];

    /**
     * confirmed ,必須有pwd_confirmation,並且和pwd相同
     * regex The password contains characters from at least three categories:
     * English uppercase characters (A – Z)
     * English lowercase characters (a – z)
     * Base 10 digits (0 – 9)
     * @return  array
     */
    public static function rules()
    {
        return $rules = [
            'account' => ['required', 'string' , 'min:6', 'max:30', 'regex:/^[a-zA-Z0-9_]*$/', 'unique:admin'],
            'pwd' => [
                'required',
                'min:6',
                'max:20',
                'regex:/^((?=.*[A-Za-z0-9])(?=.*[A-Z])(?=.*[a-z]))^.*$/',
                'confirmed'
            ],
            'name' => 'required|string|min:1|max:30',
            'email' => 'email'
        ];
    }

    public static function updateRules()
    {
        return $updateRules = [
            'id' => 'exists:admin,id',
            'name' => 'required|string|min:3|max:30',
            'pwd' => ['min:6',
                'max:20',
                'regex:/^((?=.*[A-Za-z0-9])(?=.*[A-Z])(?=.*[a-z]))^.*$/',
                'confirmed'
            ],
            'status' => 'required|integer|in:0,1,9',
            'email' => 'email'
        ];
    }

    //validate rules
    public static function searchRules()
    {
        return $searchRules = [
            'status' => 'integer|in:0,1'
        ];
    }

    //不可批量賦值的欄位
    protected $guarded = [];

    public function AdminSystemFeature()
    {
        return $this->hasMany(AdminSystemFeature::class, 'user', 'account');
    }
//    protected $dateFormat = '';
}