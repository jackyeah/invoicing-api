<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/12/5
 * Time: 下午 4:13
 */

namespace App\Http\RepositoryProtocol;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Festival extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    protected $table = 'festival';

    public $timestamps = false;

    public static $rules = [
        'status' => 'required|boolean',
        'promotionCode' => 'required|exists:promotion_station,code'
    ];

    public static $check_rules = [
        'promotionCode' => 'required|exists:festival,promotion_code'
    ];

    public static function boot()
    {
        Festival::creating(function ($festival) {
            $festival->updated_at = date('Y-m-d H:i:s');
            $festival->mod_user = Auth::user()['account'];
        });
    }

}