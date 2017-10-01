<?php
$_SERVER['HTTP_ACCEPT_ENCODING'] = '';
$_SERVER['HTTP_HOST'] = '127.0.0.1';
$_SERVER['PHP_SELF'] = '/';
require_once dirname(__FILE__) . '/../Public/init.php';
DI()->loader->addDirs('Common');
!empty(getopt('a:')['a']) or die('请输入正确参数');
$do = getopt('a:')['a'];
set_time_limit(0);
ignore_user_abort(true);
try {
	switch ($do) {
		case 'sign':
			DI()->logger->info('执行贴吧定时，签到');
			Domain_Tieba::doSignAll();//签到所有贴吧
			DI()->logger->info('执行贴吧定时，签到重试');
			Domain_Tieba::doRetryAll();//重试所有出错贴吧
			break;
		default:
			break;
	}

} catch (Exception $e) {
	echo $e->getMessage();
}

