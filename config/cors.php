<?php


return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout'],

    'allowed_methods' => ['*'],

    /*
     * Cuando supports_credentials = true, el navegador EXIGE que
     * Access-Control-Allow-Origin sea el origen exacto (no '*').
     * Listar todos los orígenes de desarrollo válidos.
     */
    'allowed_origins' => array_filter(array_map('trim', explode(',', (string) env(
        'CORS_ALLOWED_ORIGINS',
        'http://localhost:3000,http://127.0.0.1:3000,http://localhost:8080,http://127.0.0.1:8080'
    )))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    /*
     * Exponer el header X-Cart-Token para que el SPA pueda leerlo
     * desde JavaScript y reenviarlo en peticiones posteriores.
     */
    'exposed_headers' => ['X-Cart-Token'],

    'max_age' => 0,

    'supports_credentials' => true,

];
