<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2017/6/23
 * Time: 下午 10:09
 */
define('IS_JSON',true);

//echo $_GET['echostr'];
//die();

$_SERVER["HTTP_ACCEPT_ENCODING"] = ''; // gzip判断
$GLOBALS['HTTP_RAW_POST_DATA']=file_get_contents("php://input"); // PHP7.0
if (!isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
	die('Access denied!');
}

require_once dirname(__FILE__) . '/Public/init.php';

//装载项目代码和扩展类库
DI()->loader->addDirs(array('Common', 'Library'));

/** ---------------- 微信轻聊版 ---------------- **/

$robot = new Wechat_Lite('LYiHo', true);
$rs = $robot->response();
$rs->output();