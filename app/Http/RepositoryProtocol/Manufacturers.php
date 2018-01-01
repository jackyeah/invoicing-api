<?php
/**
 * Created by PhpStorm.
 * User: frogyeh
 * Date: 2017/12/26
 * Time: 下午9:46
 */

namespace App\Http\RepositoryProtocol;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Manufacturers extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    protected $table = 'manufacturers';

    public $timestamps = false;

    public static $rules = [];
}