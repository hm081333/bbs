<?php
/**
 * 以下配置为系统级的配置，通常放置不同环境下的不同配置
 *
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2017-07-13
 */

return [
    /**
     * 默认环境配置
     */
    'debug' => true,

    /**
     * 加密
     */
    'crypt' => [
        'mcrypt_iv' => '12345678',      //8位
    ],

    'openssl' => [
        'config' => [
            'config' => "D:\phpStudy\Apache\conf\openssl.cnf",// 自定义 openssl.conf 文件的路径
            "digest_alg" => "sha256",// 摘要算法或签名哈希算法
            "private_key_bits" => 2048,// 指定应该使用多少位来生成私钥
            "private_key_type" => OPENSSL_KEYTYPE_RSA,// 选择在创建CSR时应该使用哪些扩展
        ],
        'privateKey' => API_ROOT . '/config/bbs_private.pem',
        'publicKey' => API_ROOT . '/config/bbs_public.pem',
    ],

    /**
     * 缓存配置
     */
    'cache' => [
        // 文件缓存
        'file' => [
            // 缓存文件保存路径
            'path' => API_ROOT . '/Runtime',
            // 缓存前缀
            'prefix' => 'bbs',
        ],
        // MC缓存服务器参考配置
        'memcache' => [
            // Memcache域名，多个用英文逗号分割
            'host' => '127.0.0.1',
            // Memcache端口，多个用英文逗号分割
            'port' => 11211,
            // Memcache权重，多个用英文逗号分割
            'weight' => 0,
            // Memcache key prefix
            'prefix' => 'bbs_',
        ],
        // Redis缓存服务器参考配置
        'redis' => [
            // Redis连接方式 unix,http
            'type' => 'http',
            // unix方式连接时，需要配置
            'socket' => '',
            // Redis域名
            'host' => '127.0.0.1',
            // Redis端口,默认为6379
            'port' => 6379,
            // Redis key prefix
            'prefix' => 'bbs:',
            // Redis 身份验证
            'auth' => '',
            // Redis库,默认0
            'db' => 0,
            // 连接超时时间,单位秒,默认300
            'timeout' => 300,
        ],
    ],

    /**
     * COOKIE配置
     */
    'cookie' => [
        // Cookie有效的服务器路径
        'path' => '/',
        // Cookie的有效域名/子域名
        'domain' => null,
        // 是否仅仅通过安全的HTTPS连接传给客户端
        'secure' => false,
        // 是否仅可通过HTTP协议访问
        'httponly' => false,
        // 加解密服务，须实现PhalApi\Crypt接口
        'crypt' => null,
        // crypt使用的密钥
        'key' => null,
    ],

    /**
     * 网址前缀 和 目录名 对应的 命名空间名称
     */
    'moduleRule' => [
        'default' => 'bbs',
        'prefix' => [
            // 'app' => 'App',
            'bbs' => 'Bbs',
            'sign' => 'Sign',
            'admin' => 'Admin',
            'common' => 'Common',
        ],
    ],

    /**
     * 跨域请求规则
     */
    'Cross-origin' => [
        'open' => false,
        // 允许请求的地址
        'Access-Control-Allow-Origin' => [
            'http://localhost:8080',
            'http://bbs.lyihe2.tk:8080',
            'http://192.168.1.135:8080',
        ],
        // 允许请求的Headers
        'Access-Control-Allow-Headers' => [
            'Accept',
            'Content-Type',
            'X-Requested-With',
            'Isapp',
            'Authorization',
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
