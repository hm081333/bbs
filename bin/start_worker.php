<?php
/**
 * run with command
 * php start.php start
 */

// ini_set('display_errors', 'on');
defined('API_ROOT') || define('API_ROOT', dirname(dirname(__FILE__)));

require_once API_ROOT . '/public/init.php';

use Workerman\Worker;

if (strpos(strtolower(PHP_OS), 'win') === 0) {
    exit("start.php not support windows, please use start_for_win.bat\n");
}

// 检查扩展
if (!extension_loaded('pcntl')) {
    exit("Please install pcntl extension. See http://doc3.workerman.net/appendices/install-extension.html\n");
}

if (!extension_loaded('posix')) {
    exit("Please install posix extension. See http://doc3.workerman.net/appendices/install-extension.html\n");
}

// 标记是全局启动
define('GLOBAL_START', 1);

require_once API_ROOT . '/vendor/autoload.php';

// 加载所有Applications/*/start.php，以便启动所有服务
foreach (glob(API_ROOT . '/Library/WorkerMan/start*.php') as $start_file) {
    require_once $start_file;
}
// 运行所有服务
Worker::runAll();
