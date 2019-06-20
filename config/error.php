<?php
/**
 * 捕获异常
 * User: LYi-Ho
 * Date: 2018/11/24
 * Time: 22:26
 */

/**
 * 捕获错误异常
 */
function fatal_handler()
{
    $error = error_get_last();
    if ($error && ($error["type"] === ($error["type"] & (E_ERROR | E_USER_ERROR | E_CORE_ERROR |
                    E_COMPILE_ERROR | E_RECOVERABLE_ERROR | E_PARSE)))) {
        $errno = $error["type"];
        $errfile = $error["file"];
        $errline = $error["line"];
        $errstr = $error["message"];
        // if (PhalApi\DI()->notorm->inTransaction(DB_DS)) {
        //     PhalApi\DI()->logger->info('数据库事务中断');
        //     PhalApi\DI()->notorm->rollback(DB_DS);
        // }
        error_handler($errno, $errstr, $errfile, $errline, 'ERROR_FATAL|');
    }
}


/**
 * 捕获警告
 * @param $errno
 * @param $errstr
 * @param $errfile
 * @param $errline
 */
function error_handler($errno, $errstr, $errfile, $errline, $type = 'STRICT_REEOR|')
{
    if (is_array($type)) {
        $type = 'STRICT_REEOR|';
    }
    \Common\DI()->logger->error($type . $errno . '   ' . $errstr . "\n Error on line   " . $errfile . '   ' . $errline . "\n");
}

register_shutdown_function('fatal_handler');// 捕获系统级错误
set_error_handler('error_handler');// 捕获一般错误
