<?php
/**
 * $APP_NAME 统一入口
 */

require_once dirname(__FILE__) . '/Public/init.php';

//装载你的接口
DI()->loader->addDirs('Bbs');
DI()->loader->addDirs('Common');

DI()->view = new View_Lite('Bbs');

/** ---------------- 响应接口请求 ---------------- **/

$api = new PhalApi();
$rs = $api->response();
$rs->output();
