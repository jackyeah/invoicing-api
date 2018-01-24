<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2018/1/24
 * Time: 下午 6:03
 */

namespace App\Http\RepositoryProtocol;

use App\Http\RepositoryProtocol\Traits\RewriteTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ShippingRecord extends Model
{
    use RewriteTrait;

    public function __construct()
    {
        parent::__construct();
    }

    protected $table = 'shipping_record';
    public $timestamps = false;

    public static $update_rules = [
        'id' => 'required|exists:shipping_method,id',
        'name' => 'required|min:2|max:100'
    ];

    public static function boot()
    {
        ShippingRecord::creating(function ($shipping_record) {
            $shipping_record->updated_at = date('Y-m-d H:i:s');
            $shipping_record->mod_user = Auth::user()['account'];
        });
    }

}