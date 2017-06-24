<?php
/**
 * 统一初始化
 */
 
/** ---------------- 根目录定义，自动加载 ---------------- **/

date_default_timezone_set('Asia/Shanghai');

defined('API_ROOT') || define('API_ROOT', dirname(__FILE__) . '/..');

require_once API_ROOT . '/PhalApi/PhalApi.php';
//require_once API_ROOT . '/Library/View/Lite.php'; // 视图控制器
//require_once API_ROOT . '/Library/Google_Authenticator/GoogleAuthenticator.php'; // 谷歌二次验证
require_once API_ROOT . '/Library/upload_image/upload_image.php'; // 简陋的图片上传function
//require_once API_ROOT . '/Config/constant.php'; // 常量

session_start();

$loader = new PhalApi_Loader(API_ROOT, 'Library');

/** ---------------- 注册&初始化 基本服务组件 ---------------- **/

// 自动加载
DI()->loader = $loader;

// 配置
DI()->config = new PhalApi_Config_File(API_ROOT . '/Config');

DI()->config->get('constant'); // 常量

// 调试模式，$_GET['__debug__']可自行改名
DI()->debug = !empty($_GET['__debug__']) ? true : DI()->config->get('sys.debug');

if (DI()->debug) {
    // 启动追踪器
    DI()->tracer->mark();

    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
}

// 日记纪录
DI()->logger = new PhalApi_Logger_File(API_ROOT . '/Runtime', PhalApi_Logger::LOG_LEVEL_DEBUG | PhalApi_Logger::LOG_LEVEL_INFO | PhalApi_Logger::LOG_LEVEL_ERROR);

// 数据操作 - 基于NotORM
DI()->notorm = new PhalApi_DB_NotORM(DI()->config->get('dbs'), DI()->debug);

if(!defined('IS_JSON')){
	$accept = DI()->request->getHeader('Accept');
	$accept = explode(',',$accept);
	$accept = $accept[0];
	if($accept=='text/html'||$accept=='text/plain'){
		defined('IS_JSON')||define('IS_JSON',false);
		DI()->response = 'PhalApi_Response_Explorer';
	}else{
		defined('IS_JSON')||define('IS_JSON',true);
		DI()->response = 'PhalApi_Response_Json';
	}
}

// 翻译语言包设定
if (isset($_SESSION['Language'])) {
	$language = GL();
	if ($_SESSION['Language'] != $language) {
		SL($_SESSION['Language']);
	}
	unset($language);
} else {
	SL('zh_cn');
}

/** ---------------- 定制注册 可选服务组件 ---------------- **/

/**
// 签名验证服务
DI()->filter = 'PhalApi_Filter_SimpleMD5';
 */

/**
// 缓存 - Memcache/Memcached
DI()->cache = function () {
    return new PhalApi_Cache_Memcache(DI()->config->get('sys.mc'));
};
 */

/**
// 支持JsonP的返回
if (!empty($_GET['callback'])) {
    DI()->response = new PhalApi_Response_JsonP($_GET['callback']);
}
 */
