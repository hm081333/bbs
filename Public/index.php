<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2018/7/21
 * Time: 23:06
 */

$module = isset($_SERVER['REDIRECT_URL']) ? substr($_SERVER['REDIRECT_URL'], 1) : '';
defined('MODULE') || define('MODULE', $module == '' ? 'bbs' : $module);
require_once dirname(__FILE__) . '/init.php';

//装载你的接口
DI()->loader->addDirs(ucfirst(MODULE));
DI()->loader->addDirs('Common');

DI()->view = new View_Lite(ucfirst(MODULE));

/** ---------------- 响应接口请求 ---------------- **/

$user_token = DI()->cookie->get(USER_TOKEN);
if (!empty($user_token) && empty($_SESSION['user_id'])) {
    $user = Domain_User::user();
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['user_name'];
        $_SESSION['user_auth'] = $user['auth'];
    }
}

$api = new PhalApi();
$rs = $api->response();
$rs->output();
