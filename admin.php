<?php
/**
 * $APP_NAME 统一入口
 */

require_once dirname(__FILE__) . '/Public/init.php';

//装载你的接口
DI()->loader->addDirs('Admin');
DI()->loader->addDirs('Common');

DI()->view = new View_Lite('Admin');

/** ---------------- 响应接口请求 ---------------- **/

//过滤后台未登陆的操作
if (empty($_SESSION['admin_id']) && !empty($_GET['service']) && $_GET['service'] != 'Public.setLanguage') {
	echo("<script>alert('".T('未登录')."');window.location.href='./admin.php'</script>");
	exit;
}

$api = new PhalApi();
$rs = $api->response();
$rs->output();
