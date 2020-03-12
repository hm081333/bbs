<?php
/**
 * 统一初始化
 */

// 定义项目路径
// defined('API_ROOT') || define('API_ROOT', dirname(__FILE__) . '/..');
defined('API_ROOT') || define('API_ROOT', dirname(dirname(__FILE__)));
// 是否命令行运行
defined('IS_CLI') || define('IS_CLI', (PHP_SAPI == 'cli') ? true : false);
// 是否ajax请求
defined('IS_AJAX') || define('IS_AJAX', ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) ? true : false);
// POST请求的key 携带加密后数据 - md5后的32位字符串
defined('POST_KEY') || define('POST_KEY', 'abac49e1f519fe724f10e4fb40cf0a38');

// 应付命令行执行时无法找到HTTP_HOST下标导致报错
if (IS_CLI) {
    $_SERVER['HTTP_ACCEPT_ENCODING'] = '';
    $_SERVER['HTTP_HOST'] = '127.0.0.1';
    $_SERVER['PHP_SELF'] = '/';
}
defined('URL_ROOT') || define('URL_ROOT', (isset($_SERVER['HTTPS']) && 'on' === $_SERVER['HTTPS'] ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . (dirname(dirname($_SERVER['PHP_SELF'])) == '\\' ? '' : dirname($_SERVER['PHP_SELF'])));

if (!file_exists(API_ROOT . '/vendor/autoload.php')) {
    exit('请先运行 composer install');
}
// 引入composer
require_once API_ROOT . '/vendor/autoload.php';

// 时区设置
date_default_timezone_set('Asia/Shanghai');

// 引入 igbinary
include API_ROOT . '/config/igbinary.php';

// 引入GZIP
include API_ROOT . '/config/gzip.php';

// 引入DI服务
include API_ROOT . '/config/di.php';

// 引入微信公众号服务
include API_ROOT . '/config/wechat.php';

if (!IS_CLI) {
    // 引入伪静态服务 重写请求服务
    include API_ROOT . '/config/url_rewrite.php';
    // 引入跨域处理
    include API_ROOT . '/config/cors.php';
}

// 引入常量
include API_ROOT . '/config/constant.php';

if (!IS_CLI) {
    // 配置Session
    include API_ROOT . '/config/session.php';
}

// 翻译语言包设定
\PhalApi\SL('zh_cn');
