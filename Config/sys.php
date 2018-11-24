<?php
/**
 * 以下配置为系统级的配置，通常放置不同环境下的不同配置
 */


return [
    /**
     * 默认环境配置
     */
    'debug' => TRUE,

    /**
     * MC缓存服务器参考配置
     */
    'mc' => [
        'host' => '127.0.0.1',
        'port' => 11211,
    ],

    /**
     * 加密
     */
    'crypt' => [
        'mcrypt_iv' => '12345678',      //8位
    ],

    /**
     * 文件日志配置
     */
    'file' => [
        'path' => API_ROOT . '/Runtime',
    ],

    /**
     * 翻译功能开关
     */
    'translate' => FALSE,

    /**
     * 跨域请求规则
     */
    'crossDomain' => [
        // 允许请求的地址
        // 'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Origin' => [
            'http://localhost:8080',
            'http://bbs.lyihe2.tk:8080',
        ],
        // 允许请求的Headers
        // 'Access-Control-Allow-Headers' => '*',
        'Access-Control-Allow-Headers' => [
            'Content-Type',
            'Isapp',
        ],
        // 允许请求的方式
        'Access-Control-Allow-Methods' => [
            'OPTIONS',
            'GET',
            'POST',
        ],
        // 客户端携带证书式访问，可以保持跨域 Ajax 时的 Cookie
        'Access-Control-Allow-Credentials' => 'true',
    ],

];
