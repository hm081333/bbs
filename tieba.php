<?php
/**
 * $APP_NAME 统一入口
 */

define('tieba', true);
defined('website') || define('website', 'tieba');

require_once dirname(__FILE__) . '/Public/init.php';

//装载你的接口
DI()->loader->addDirs('Tieba');
DI()->loader->addDirs('Common');

DI()->view = new View_Lite('Tieba');

$user_token = DI()->cookie->get(USER_TOKEN);
if (!empty($user_token) && empty($_SESSION['user_id'])) {
	$user = unserialize($user_token);
	$_SESSION['user_id'] = $user['id'];
	$_SESSION['user_name'] = $user['user_name'];
	$_SESSION['user_auth'] = $user['auth'];
}

/*if (DI()->tool->is_weixin() && !isset($_SESSION['user_id'])) {
	$wechat_domain = new Domain_Wechat();
	if (isset($_GET['code'])) {
		$wechat_domain->getOpenId($_GET['code']);
	} else {
		$wechat_domain->getOpenIdCode();
	}
}*/

/** ---------------- 响应接口请求 ---------------- **/

//过滤前台未登陆的操作
if (empty($_SESSION['user_id']) && !empty($_GET['service']) && strpos('Tieba.DoSignAll', $_GET['service']) === false) {
	echo("<script>alert('" . T('未登录') . "');window.location.href='./tieba.php'</script>");
}

$api = new PhalApi();
$rs = $api->response();
$rs->output();
