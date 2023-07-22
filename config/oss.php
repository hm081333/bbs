<?php

return [
    'default' => 'aliyun',
    'configs' => [
        'aliyun' => [
            'access_key_id' => env('ALIYUN_OSS_KEY'),
            'access_key_secret' => env('ALIYUN_OSS_SECRET'),
            'endpoint' => env('ALIYUN_OSS_ENDPOINT'),
            'bucket' => env('ALIYUN_OSS_BUCKET'),
        ],
    ],
];

