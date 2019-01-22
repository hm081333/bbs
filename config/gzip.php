<?php
/**
 * 开启GZIP
 * User: LYi-Ho
 * Date: 2018/11/24
 * Time: 14:41
 */

//开启GZIP
if (!headers_sent() && extension_loaded("zlib") && strstr($_SERVER["HTTP_ACCEPT_ENCODING"], "gzip")) {//开启gzip压缩
    ini_set('zlib.output_compression', 'On');
    ini_set('zlib.output_compression_level', '6');
}
