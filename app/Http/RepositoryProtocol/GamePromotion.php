<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/11/28
 * Time: 上午 11:53
 */

namespace App\Http\RepositoryProtocol;

use Illuminate\Database\Eloquent\Model;

class GamePromotion extends Model
{

    public function __construct()
    {
        parent::__construct();
    }

    protected $table = 'game_promotion';

    public $timestamps = false;

    public static $rules = [];

    public function game_list()
    {
        return $this->hasMany('App\Http\RepositoryProtocol\GameList', 'id', 'game_list_id');
    }

    public function ranking_game()
    {
        return $this->hasMany('App\Http\RepositoryProtocol\RankingGame', 'game_promotion_id');
    }

    public function ranking_user()
    {
        return $this->hasMany('App\Http\RepositoryProtocol\RankingUser', 'game_promotion_id');
    }

}