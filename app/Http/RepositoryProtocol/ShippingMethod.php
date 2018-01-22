<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2018/1/20
 * Time: 下午 3:58
 */

namespace App\Http\RepositoryProtocol;

use App\Http\RepositoryProtocol\Traits\RewriteTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ShippingMethod extends Model
{
    use RewriteTrait;

    public function __construct()
    {
        parent::__construct();
    }

    protected $table = 'shipping_method';
    public $timestamps = false;

    public static $create_rules = [
        'name' => 'required|min:2|max:100'
    ];

    public static $update_rules = [
        'id' => 'required|exists:shipping_method,id',
        'name' => 'required|min:2|max:100'
    ];

    public static $delete_rules = [
        'id' => 'required|exists:shipping_method,id'
    ];

    public static function boot()
    {
        ShippingMethod::creating(function ($shipping_method) {
            $shipping_method->updated_at = date('Y-m-d H:i:s');
            $shipping_method->mod_user = Auth::user()['account'];
        });
    }


}