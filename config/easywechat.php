<?php
// https://easywechat.com/6.x/index.html
return [
    /*
     * 默认配置，将会合并到各模块中
     */
    'defaults' => [
        'http' => [
            'timeout' => 5.0,
        ],
    ],

    /*
     * 公众号
     */
    //'official_account' => [
    //    'default' => [
    //        'app_id' => env('WECHAT_OFFICIAL_ACCOUNT_APPID', ''),     // AppID
    //        'secret' => env('WECHAT_OFFICIAL_ACCOUNT_SECRET', ''),    // AppSecret
    //        'token' => env('WECHAT_OFFICIAL_ACCOUNT_TOKEN', ''),     // Token
    //        'aes_key' => env('WECHAT_OFFICIAL_ACCOUNT_AES_KEY', ''),   // EncodingAESKey
    //
    //        /*
    //         * OAuth 配置
    //         *
    //         * scopes：公众平台（snsapi_userinfo / snsapi_base），开放平台：snsapi_login
    //         * callback：OAuth授权完成后的回调页地址(如果使用中间件，则随便填写。。。)
    //         * enforce_https：是否强制使用 HTTPS 跳转
    //         */
    //        // 'oauth'   => [
    //        //     'scopes'        => array_map('trim', explode(',', env('WECHAT_OFFICIAL_ACCOUNT_OAUTH_SCOPES', 'snsapi_userinfo'))),
    //        //     'callback'      => env('WECHAT_OFFICIAL_ACCOUNT_OAUTH_CALLBACK', '/examples/oauth_callback.php'),
    //        //     'enforce_https' => true,
    //        // ],
    //
    //        /**
    //         * 接口请求相关配置，超时时间等，具体可用参数请参考：
    //         * https://github.com/symfony/symfony/blob/6.0/src/Symfony/Contracts/HttpClient/HttpClientInterface.php#L26
    //         */
    //        //'http' => [
    //        //  'timeout' => 5.0,
    //        //   // 如果你在国外想要覆盖默认的 url 的时候才使用，根据不同的模块配置不同的 uri
    //        //  'base_uri' => 'https://api.weixin.qq.com/',
    //        //],
    //    ],
    //],

    /*
     * 开放平台第三方平台
     */
    // 'open_platform' => [
    //     'default' => [
    //         'app_id'     => env('WECHAT_OPEN_PLATFORM_APPID', ''),
    //         'secret'     => env('WECHAT_OPEN_PLATFORM_SECRET', ''),
    //         'token'      => env('WECHAT_OPEN_PLATFORM_TOKEN', ''),
    //         'aes_key'    => env('WECHAT_OPEN_PLATFORM_AES_KEY', ''),

    /**
     * 接口请求相关配置，超时时间等，具体可用参数请参考：
     * https://github.com/symfony/symfony/blob/6.0/src/Symfony/Contracts/HttpClient/HttpClientInterface.php#L26
     */
    //          'http' => [
    //            'timeout' => 5.0,
    //             // 如果你在国外想要覆盖默认的 url 的时候才使用，根据不同的模块配置不同的 uri
    //            'base_uri' => 'https://api.weixin.qq.com/',
    //          ],
    //     ],
    // ],

    /*
     * 小程序
     */
    'mini_app' => [
        'default' => [
            'app_id' => env('WECHAT_MINI_APP_APPID', ''),
            'secret' => env('WECHAT_MINI_APP_SECRET', ''),
            'token' => env('WECHAT_MINI_APP_TOKEN', ''),
            'aes_key' => env('WECHAT_MINI_APP_AES_KEY', ''),

            /**
             * 接口请求相关配置，超时时间等，具体可用参数请参考：
             * https://github.com/symfony/symfony/blob/6.0/src/Symfony/Contracts/HttpClient/HttpClientInterface.php#L26
             */
            'http' => [
                'timeout' => 5.0,
                // 如果你在国外想要覆盖默认的 url 的时候才使用，根据不同的模块配置不同的 uri
                'base_uri' => 'https://api.weixin.qq.com/',
            ],
        ],
    ],

    /*
     * 微信支付
     */
    'pay' => [
        'default' => [
            'app_id' => env('WECHAT_PAY_APPID', ''),
            'mch_id' => env('WECHAT_PAY_MCH_ID', ''),
            // 商户证书
            'private_key' => storage_path('certs/wechat-pay/apiclient_key.pem'),
            'certificate' => storage_path('certs/wechat-pay/apiclient_cert.pem'),
            // 默认支付结果通知地址
            'notify_url' => 'https://wemini.gdsbuild.com/common/notify/wechatPay',
            /**
             * 证书序列号，可通过命令从证书获取：
             * `openssl x509 -in apiclient_cert.pem -noout -serial`
             * `openssl x509 -in apiclient_cert.pem -noout -serial | awk -F= '{print $2}'`
             */
            'certificate_serial_no' => env('WECHAT_PAY_CERTIFICATE_SERIAL_NO', ''),

            'http' => [
                'base_uri' => 'https://api.mch.weixin.qq.com/',
            ],

            // v2 API 秘钥
            'v2_secret_key' => env('WECHAT_PAY_V2_SECRET_KEY', ''),

            // v3 API 秘钥
            'secret_key' => env('WECHAT_PAY_V3_SECRET_KEY', ''),

            // 平台证书：微信支付 APIv3 平台证书，需要使用工具下载
            // 注意 此处为微信支付平台证书 https://pay.weixin.qq.com/wiki/doc/apiv3/apis/wechatpay5_1.shtml
            // 下载工具：https://github.com/wechatpay-apiv3/CertificateDownloader
            'platform_certs' => [
                storage_path('certs/wechat-pay/platform_key.pem'),
            ],
        ],
    ],

    /*
     * 企业微信
     */
    // 'work' => [
    //     'default' => [
    //         'corp_id'    => env('WECHAT_WORK_CORP_ID', ''),
    //         'secret'     => env('WECHAT_WORK_SECRET', ''),
    //         'token'      => env('WECHAT_WORK_TOKEN', ''),
    //         'aes_key'    => env('WECHAT_WORK_AES_KEY', ''),

    /**
     * 接口请求相关配置，超时时间等，具体可用参数请参考：
     * https://github.com/symfony/symfony/blob/6.0/src/Symfony/Contracts/HttpClient/HttpClientInterface.php#L26
     */
    //          'http' => [
    //            'timeout' => 5.0,
    //             // 如果你在国外想要覆盖默认的 url 的时候才使用，根据不同的模块配置不同的 uri
    //            'base_uri' => 'https://api.weixin.qq.com/',
    //          ],
    //      ],
    // ],

    /*
     * 企业微信开放平台
     */
    // 'open_work' => [
    //     'default' => [
    //         'corp_id'            => env('WECHAT_OPEN_WORK_CORP_ID', ''),
    //         'provider_secret'    => env('WECHAT_OPEN_WORK_SECRET', ''),
    //         'token'              => env('WECHAT_OPEN_WORK_TOKEN', ''),
    //         'aes_key'            => env('WECHAT_OPEN_WORK_AES_KEY', ''),

    /**
     * 接口请求相关配置，超时时间等，具体可用参数请参考：
     * https://github.com/symfony/symfony/blob/6.0/src/Symfony/Contracts/HttpClient/HttpClientInterface.php#L26
     */
    //          'http' => [
    //            'timeout' => 5.0,
    //             // 如果你在国外想要覆盖默认的 url 的时候才使用，根据不同的模块配置不同的 uri
    //            'base_uri' => 'https://api.weixin.qq.com/',
    //          ],
    //      ],
    // ],
];