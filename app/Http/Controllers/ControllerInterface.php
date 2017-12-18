<?php
namespace App\Http\Controllers;

interface ControllerInterface
{
    /**
     * @return mixed
     */
    public function success();

    /**
     * @param $error_code
     * @return mixed
     */
    public function fail($error_code);

}