<?php
/**
 * Created by PhpStorm.
 * User: frogyeh
 * Date: 2017/12/27
 * Time: 下午8:03
 */

namespace App\Http\Middleware;

//use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'stripe/*',
    ];

    /*protected $except = [
        'payment/verify/{id}/*',
    ];*/
}