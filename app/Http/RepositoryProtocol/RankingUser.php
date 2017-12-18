<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/11/27
 * Time: 上午 11:40
 */

namespace App\Http\RepositoryProtocol;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class RankingUser extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    protected $table = 'ranking_user';

    public $timestamps = false;

    public static $rules = [];

    /*public static function boot()
    {
        RankingGame::saving(function ($rankingUser) {
            $rankingUser->updated_at = date('Y-m-d H:i:s');
            $rankingUser->mod_user = Auth::user()['account'];
        });
        RankingGame::creating(function ($rankingUser) {
            $rankingUser->updated_at = date('Y-m-d H:i:s');
            $rankingUser->mod_user = Auth::user()['account'];
        });
        return true;
    }*/

}