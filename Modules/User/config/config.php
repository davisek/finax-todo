<?php

return [
    'name' => 'User',

    'access_token_ttl' => env('ACCESS_TOKEN_TTL', 15),
    'refresh_token_ttl' => env('REFRESH_TOKEN_TTL', 7),
    'rotate_refresh_token' => env('ROTATE_REFRESH_TOKEN', true),

    'refresh_cookie_path' => '/api/v1/auth/refresh',
    'refresh_cookie_domain'   => env('REFRESH_COOKIE_DOMAIN', null),
    'refresh_cookie_secure'   => env('REFRESH_COOKIE_SECURE', app()->environment('production')),
    'refresh_cookie_samesite' => env('REFRESH_COOKIE_SAMESITE', 'Lax'),
];
