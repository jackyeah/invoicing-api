<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/11/23
 * Time: 上午 10:39
 */

namespace App\Http\RepositoryProtocol;

use App\Http\Repository\Traits\TryCatchTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class GameList extends Model
{

    public function __construct()
    {
        parent::__construct();
    }

    protected $table = 'game_list';

    public $timestamps = false;

    public static $search_rules = [
        'promotionCode' => 'required|exists:promotion_station,code'
    ];

    public static $rules = [
        'name' => 'required|string|min:1|max:50',
        'promotionCode' => 'required|json',
        'pic' => 'required|string'
    ];

    public static $upd_rules = [
        'game_id' => 'required|exists:game_list,id',
        'name' => 'required|string|min:1|max:50',
        'promotionCode' => 'required|string',
        'pic' => 'required|string'
    ];

    public static $del_rules = [
        'game_id' => 'required|exists:game_list,id'
    ];

    public static function boot()
    {
        GameList::saving(function ($gameList) {
            $gameList->updated_at = date('Y-m-d H:i:s');
            $gameList->mod_user = Auth::user()['account'];
        });
        GameList::creating(function ($gameList) {
            $gameList->updated_at = date('Y-m-d H:i:s');
            $gameList->mod_user = Auth::user()['account'];
        });

        GameList::deleting(function ($gameList) {
            $gameList->game_promo()->delete();
        });
    }

    public function game_promo()
    {
        return $this->hasMany('App\Http\RepositoryProtocol\GamePromotion', 'game_list_id');
    }

}