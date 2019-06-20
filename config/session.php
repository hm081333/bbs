<?php
/**
 * Session配置
 * User: LYi-Ho
 * Date: 2018/11/24
 * Time: 14:41
 */

if (!isset($_SESSION) && !IS_CLI) {
    if (class_exists('Redis')) {
        ini_set('session.save_handler', 'redis');
        ini_set('session.save_path', 'tcp://127.0.0.1:6379');
    }
    // session_set_save_handler(new Model_Session(), true);
    session_name(SESSION_NAME);
    session_start();//开启缓存
}
