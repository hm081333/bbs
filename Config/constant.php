<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2017/5/29
 * Time: 下午 1:30
 */
defined('DB') || define('DB', 'db_forum');
defined('each_page') || define('each_page', 8);
defined('NOW_TIME') || define('NOW_TIME', isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time());
defined('back') || define('back', TRUE);
defined('IS_AJAX') || define('IS_AJAX', ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) ? true : false);
defined('MySQL') || define('MySQL', PHP_OS == 'WINNT' ? "C:\\phpStudy\\MySQL\\bin\\" : "");
defined('PREFIX') || define('PREFIX', DI()->config->get('dbs.tables')['__default__']['prefix']);
//http://localhost/1/test/?service=Default.index&page=1
/*<?php echo T(''); ?>*/


