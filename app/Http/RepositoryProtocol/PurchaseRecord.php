<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2018/1/17
 * Time: 下午 5:55
 */

namespace App\Http\RepositoryProtocol;

use App\Http\RepositoryProtocol\Traits\RewriteTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class PurchaseRecord extends Model
{
    use RewriteTrait;

    public function __construct()
    {
        parent::__construct();
    }

    protected $table = 'purchase_record';
    public $timestamps = false;

    public static function boot()
    {
        ProductStyle::creating(function ($purchase_record) {
            $purchase_record->updated_at = date('Y-m-d H:i:s');
            $purchase_record->mod_user = Auth::user()['account'];
        });
    }
}