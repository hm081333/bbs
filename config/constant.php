<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2017/5/29
 * Time: 下午 1:30
 */
defined('DB') || define('DB', $di->config->get('dbs.tables.__default__.map.0.db'));
// 单页显示行数
defined('PAGE_NUM') || define('PAGE_NUM', 10);
// 当前时间：请求时间 或 系统时间
defined('NOW_TIME') || define('NOW_TIME', isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time());
// defined('back') || define('back', false);
// 表示是否app请求
defined('ISAPP') || define('ISAPP', $di->request->getHeader('isapp', false));
// mysql路径常量
defined('MySQL') || define('MySQL', PHP_OS == 'WINNT' ? "E:\\mysql\\bin\\" : "");
// defined('PREFIX') || define('PREFIX', $di->config->get('dbs.tables')['__default__']['prefix']);
// session的key值 - md5后的字符串
defined('SESSION_NAME') || define('SESSION_NAME', '9cf3eeaffa0c8386508f932a354adf70');
// 用户token的key值 - md5后的32位字符串
defined('USER_TOKEN') || define('USER_TOKEN', '85cc39d65ded51e8ffb949503e83ed65');
// 管理员token的key值 - md5后的32位字符串
defined('ADMIN_TOKEN') || define('ADMIN_TOKEN', '1858d7d84c1f48cd63eda4f989dbf9e7');
// POST请求的key 携带加密后数据 - md5后的32位字符串
defined('POST_KEY') || define('POST_KEY', 'abac49e1f519fe724f10e4fb40cf0a38');
// 加密的密钥
defined('SECURITY_KEY') || define('SECURITY_KEY', '@fdskalhfj2387A!');
// 加密的初始化向量
defined('SECURITY_IV') || define('SECURITY_IV', '@fdfpu+adj2387A!');

