<?php
/**
 * 微信初始化
 * User: LYi-Ho
 * Date: 2018/11/24
 * Time: 22:26
 */

use EasyWeChat\Factory;

// $di = \Common\DI();

$setting = \Common\Domain\Setting::getSetting('wechat');

// 一些配置
// 参考：https://www.easywechat.com/docs/4.1/official-account/configuration
$config = [
    'app_id' => isset($setting['app_id']) ? $setting['app_id'] : '',
    'secret' => isset($setting['app_secret']) ? $setting['app_secret'] : '',
    'token' => isset($setting['token']) ? $setting['token'] : '',
    // EncodingAESKey，兼容与安全模式下请一定要填写！！！
    'aes_key' => isset($setting['aes_key']) ? $setting['aes_key'] : '',
    'response_type' => 'array',
    // 日志配置
    'log' => [
        // 日志级别, 可选为：debug/info/notice/warning/error/critical/alert/emergency
        'level' => 'debug',
        // 默认使用的 channel，生产环境可以改为下面的 prod
        'default' => 'dev',
        'channels' => [
            // 测试环境
            'dev' => [
                'driver' => 'single',
                // path：日志文件位置(绝对路径!!!)，要求可写权限
                'path' => API_ROOT . '/runtime/wechat/log/wechat.log',
                'level' => 'debug',
            ],
            // 生产环境
            'prod' => [
                'driver' => 'daily',
                // path：日志文件位置(绝对路径!!!)，要求可写权限
                'path' => API_ROOT . '/runtime/wechat/log/wechat.log',
                'level' => 'info',
            ],
        ],
    ],
];

// 公众号 - 使用配置来初始化一个公众号应用实例。
$di->wechat = Factory::officialAccount($config);

// 小程序
// $di->wechat = Factory::miniProgram($config);

// 开放平台
// $di->wechat = Factory::openPlatform($config);

// 企业微信
// $di->wechat = Factory::work($config);

// 企业微信开放平台
// $di->wechat = Factory::openWork($config);

// 微信支付
// $di->wechat = Factory::payment($config);