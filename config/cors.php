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

    'paths' => [
        // '*',
        'common/*',
        'admin/*',
        'home/*',
        'pages/*',
    ],

    'allowed_methods' => [
        // '*',
        'get',
        'post',
        'options',
    ],

    'allowed_origins' => [
        // '*',
        'http://localhost:*',
        'http://127.0.0.1:*',
        'http://*.gdsbuild.com',
        'https://*.gdsbuild.com',
    ],

    'allowed_origins_patterns' => [
        // '/http[s]?:\/\/localhost[:\d+]?/',
        // '/http[s]?:\/\/127.0.0.\d+[:\d+]?/',
        // '/http[s]?:\/\/172.(1[6-9]|2[1-9]|31).\d+.\d+[:\d+]?/',
        // '/http[s]?:\/\/192.168.\d+.\d+[:\d+]?/',
    ],

    'allowed_headers' => [
        //'*',
        'Accept',
        'Content-Type',
        'X-Requested-With',
        'Appverion',
        'AppIdCode',
        'Port',
        'Authorization',
    ],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
