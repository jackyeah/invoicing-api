<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2018/1/20
 * Time: 下午 4:18
 */

namespace App\Http\RepositoryProtocol;

use App\Http\RepositoryProtocol\Traits\RewriteTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class OrderSource extends Model
{
    use RewriteTrait;

    public function __construct()
    {
        parent::__construct();
    }

    protected $table = 'order_source';
    public $timestamps = false;

    public static $create_rules = [
        'name' => 'required|min:2|max:100'
    ];

    public static $update_rules = [
        'id' => 'required|exists:order_source,id',
        'name' => 'required|min:2|max:100'
    ];

    public static $delete_rules = [
        'id' => 'required|exists:order_source,id'
    ];

    public static function boot()
    {
        OrderSource::creating(function ($order_source) {
            $order_source->updated_at = date('Y-m-d H:i:s');
            $order_source->mod_user = Auth::user()['account'];
        });
    }
}