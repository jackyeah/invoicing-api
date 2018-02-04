<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2018/1/30
 * Time: 下午 4:37
 */

namespace App\Http\RepositoryProtocol;

use App\Http\RepositoryProtocol\Traits\RewriteTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ShippingList extends Model
{
    use RewriteTrait;

    public function __construct()
    {
        parent::__construct();
    }

    protected $table = 'shipping_list';
    public $timestamps = false;

    public static $update_rules = [
        'id' => 'required|exists:shipping_method,id',
        'name' => 'required|min:2|max:100'
    ];

    public static function boot()
    {
        ShippingList::creating(function ($shipping_list) {
            $shipping_list->updated_at = date('Y-m-d H:i:s');
            $shipping_list->mod_user = Auth::user()['account'];
        });
    }

    public function shipping_record()
    {
        return $this->hasMany('App\Http\RepositoryProtocol\ShippingRecord', 'shipping_list_id');
    }

}