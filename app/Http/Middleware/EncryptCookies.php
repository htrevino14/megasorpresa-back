<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Determina si la cookie debe ser HttpOnly.
 * XSRF-TOKEN necesita ser accesible por JS para peticiones AJAX.
 */
class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array<int, string>
     */
    protected $except = [
        'XSRF-TOKEN',
    ];

    protected function makeHttpOnlyIfShould(Cookie $cookie): Cookie
    {
        if ($cookie->getName() === 'XSRF-TOKEN') {
            return new Cookie(
                $cookie->getName(),
                $cookie->getValue(),
                $cookie->getExpiresTime(),
                $cookie->getPath(),
                $cookie->getDomain(),
                $cookie->isSecure(),
                false,
                $cookie->isRaw(),
                $cookie->getSameSite()
            );
        }

        return $cookie;
    }
}