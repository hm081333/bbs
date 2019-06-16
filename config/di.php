<?php
/**
 * DI依赖注入配置文件
 *
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2017-07-13
 */

// use PhalApi\Loader;
use PhalApi\Config\FileConfig;
use PhalApi\Logger;
use PhalApi\Logger\FileLogger;

// use PhalApi\Database\NotORMDatabase;

/** ---------------- 基本注册 必要服务组件 ---------------- **/

$di = \PhalApi\DI();

// 配置
$di->config = new FileConfig(API_ROOT . '/config');

// 调试模式，$_GET['__debug__']可自行改名
$di->debug = !empty($_GET['__debug__']) ? true : $di->config->get('sys.debug');

// 调试模式
if ($di->debug) {
    // 启动追踪器
    $di->tracer->mark('PHALAPI_INIT');
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
} else {
    ini_set('display_errors', 0);
}

// 日记纪录
$di->logger = new FileLogger(API_ROOT . '/runtime', Logger::LOG_LEVEL_DEBUG | Logger::LOG_LEVEL_INFO | Logger::LOG_LEVEL_ERROR);

// 数据操作 - 基于NotORM
// $di->notorm = new NotORMDatabase($di->config->get('dbs'), $di->debug);

// 自动加载
// $di->loader = new Loader(API_ROOT, 'Library');

// 自动加载 - 重定义
require_once API_ROOT . '/Library/Loader.php';
$di->loader = new Loader(API_ROOT, 'Library');

// 数据操作 - 基于NotORM - 重定义创建PDO实例的方法
$di->notorm = new \Database\NotORMDatabase($di->config->get('dbs'), $di->debug);

// JSON中文输出
// $di->response = new \PhalApi\Response\JsonResponse(JSON_UNESCAPED_UNICODE);

/** ---------------- 定制注册 可选服务组件 ---------------- **/

// 签名验证服务
// $di->filter = new \PhalApi\Filter\SimpleMD5Filter();

// 缓存 - Memcache/Memcached
$di->cache = function () use ($di) {
    // return new \PhalApi\Cache\MemcacheCache($di->config->get('sys.cache.memcache'));
    // return new \PhalApi\Cache\FileCache($di->config->get('sys.cache.file'));
    return new \PhalApi\Cache\RedisCache($di->config->get('sys.cache.redis'));
};

// 惰性加载Redis
/*$di->redis = function () use ($di) {
    // return new \PhalApi\Redis\Lite($di->config->get("app.redis.servers"));
    return new \PhalApi\Cache\RedisCache($di->config->get('sys.cache.redis'));
};*/

// COOKIE
$di->cookie = function () use ($di) {
    // return new \PhalApi\Cookie\MultiCookie($di->config->get('sys.cookie'));
    return new \PhalApi\Cookie($di->config->get('sys.cookie'));
};

// CURL请求
$di->curl = function () use ($di) {
    return new \PhalApi\CUrl(5);
};

//tool工具
$di->tool = function () {
    return new \PhalApi\Tool();
};

// 对称加密
$di->crypt = function () use ($di) {
    // return new \Crypt\RSA\MultiPub2PriCrypt($di->config->get('sys.openssl'));
    return new \Crypt\RSA\Pub2PriCrypt($di->config->get('sys.openssl'));
};

// 支持JsonP的返回
// if (!empty($_GET['callback'])) {
//     $di->response = new \PhalApi\Response\JsonpResponse($_GET['callback']);
// }

// 返回加密字符串
// $di->response=new \Response\JsonResponse();

// 生成二维码扩展，参考示例：?s=App.Examples_QrCode.Png
// $di->qrcode = function() {
//     return new \PhalApi\QrCode\Lite();
// };
