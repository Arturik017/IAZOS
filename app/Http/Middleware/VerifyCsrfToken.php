<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * Aici EXCLUDEM callback-ul MAIB (webhook server-to-server)
     */
    protected $except = [
        'pay/maib/callback',
    ];
}
