<?php

namespace App\Http\Services\Traits;


use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

trait ValidateTrait
{
    /**
     * @param $rules
     * @return bool
     */
    public function validateParams($rules)
    {
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            Log::error('Validate error');
            return false;
        }
        return true;
    }
}