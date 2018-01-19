<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2018/1/17
 * Time: 上午 10:56
 */

namespace App\Http\RepositoryProtocol;

use App\Http\RepositoryProtocol\Traits\RewriteTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ProductStyle extends Model
{
    use RewriteTrait;

    public function __construct()
    {
        parent::__construct();
    }

    protected $table = 'product_style';
    public $timestamps = false;

    public static function boot()
    {
        ProductStyle::creating(function ($product_style) {
            $product_style->updated_at = date('Y-m-d H:i:s');
            $product_style->mod_user = Auth::user()['account'];
        });
    }

    public function purchase_record()
    {
        return $this->hasMany('App\Http\RepositoryProtocol\PurchaseRecord', 'product_style_id');
    }

}