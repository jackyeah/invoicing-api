<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2018/1/16
 * Time: 下午 4:40
 */

namespace App\Http\RepositoryProtocol;

use App\Http\RepositoryProtocol\Traits\RewriteTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    use RewriteTrait;

    public function __construct()
    {
        parent::__construct();
    }

    protected $table = 'product';
    public $timestamps = false;

    public static function boot()
    {
        Product::creating(function ($product) {
            $product->updated_at = date('Y-m-d H:i:s');
            $product->mod_user = Auth::user()['account'];
        });
    }
}