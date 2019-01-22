<?php
/**
 * Session配置
 * User: LYi-Ho
 * Date: 2018/11/24
 * Time: 14:41
 */

if (!isset($_SESSION) && !IS_CLI) {
    // session_set_save_handler(new Model_Session(), true);
    session_name(SESSION_NAME);
    session_start();//开启缓存
}
