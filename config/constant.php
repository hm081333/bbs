<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2017/5/29
 * Time: 下午 1:30
 */
defined('DB') || define('DB', $di->config->get('dbs.tables.__default__.map.0.db'));
defined('PAGE_NUM') || define('PAGE_NUM', 10);
defined('NOW_TIME') || define('NOW_TIME', isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time());
defined('SERVER_TIME') || define('SERVER_TIME', time());
defined('back') || define('back', false);
defined('ISAPP') || define('ISAPP', $di->request->getHeader('Isapp',false));
defined('IS_AJAX') || define('IS_AJAX', ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) ? true : false);
defined('MySQL') || define('MySQL', PHP_OS == 'WINNT' ? "E:\\mariadb\\bin\\" : "");
defined('PREFIX') || define('PREFIX', $di->config->get('dbs.tables')['__default__']['prefix']);
defined('SESSION_NAME') || define('SESSION_NAME', '9cf3eeaffa0c8386508f932a354adf70');
defined('USER_TOKEN') || define('USER_TOKEN', '85cc39d65ded51e8ffb949503e83ed65');
defined('ADMIN_TOKEN') || define('ADMIN_TOKEN', '1858d7d84c1f48cd63eda4f989dbf9e7');
defined('SECURITY_KEY') || define('SECURITY_KEY', '@fdskalhfj2387A!');// 加密的密钥
// defined('SECURITY_KEY') || define('SECURITY_KEY', '0b992c709819b81f');// 加密的密钥
defined('SECURITY_IV') || define('SECURITY_IV', '@fdfpu+adj2387A!');// 加密的初始化向量
// defined('SECURITY_IV') || define('SECURITY_IV', '964aeb67601e7e84');// 加密的初始化向量

// SoundCloud 客户端 ID，如果失效请更改
// defined('MC_SC_CLIENT_ID') || define('MC_SC_CLIENT_ID', '2t9loNQH90kzJcsFCODdigxfp325aq4z');
// Curl 代理地址，解决翻墙问题。例如：define('MC_PROXY', 'http://10.10.10.10:8123');
// defined('MC_PROXY') || define('MC_PROXY', FALSE);

