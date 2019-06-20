<?php
/**
 * 开启GZIP
 * User: LYi-Ho
 * Date: 2018/11/24
 * Time: 14:41
 */

//开启 igbinary
if (function_exists('igbinary_serialize') && function_exists('igbinary_unserialize')) {//开启 igbinary
    ini_set('session.serialize_handler', 'igbinary');
}
