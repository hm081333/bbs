<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2018/8/5
 * Time: 11:29
 */

function unix_formatter($time = false, $full = false)
{
    if ($time <= 0) {
        return '-';
    }
    if ($full) {
        return date('Y-m-d H:i:s', $time);
    } else {
        return date('Y-m-d', $time);
    }
}

function url($Api = 'Default.Index', $param = [])
{
    if (is_array($param)) {
        $param = http_build_query($param);
    }
    return NOW_WEB_SITE . "?service={$Api}&{$param}";
}
