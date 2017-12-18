<?php

namespace App\Http\RepositoryProtocol;

use Illuminate\Database\Eloquent\Model;

class MultipleEssay extends Model
{

    public function __construct()
    {

    }

    public static $rules = [
        'patch_url' => 'required|url',
    ];

    protected $table = 'multiple_essay';

    protected $connection = 'festival_m';

    public $timestamps = false;
}