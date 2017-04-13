<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'telegram/334312989:AAEWEJVmWrh6XkNHKWdo_1waxE0r2G7eTjo',
        'vk',
        'vktest'
    ];
}
