<?php
/**
 * 处理跨域
 * User: LYi-Ho
 * Date: 2018/11/25
 * Time: 13:18
 */

/* 配置数组 */
$crossDomainConfig = $di->config->get('sys.Cross-origin');
if ($crossDomainConfig['open']) {
    /* 客户端域名 */
    $origin = strtolower(isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '');
    /* 追加header 开启跨域访问 */
    if ($crossDomainConfig['Access-Control-Allow-Origin'] == '*' || in_array($origin, $crossDomainConfig['Access-Control-Allow-Origin'])) {// 在可跨域的域名内
        array_walk($crossDomainConfig, function ($value, $key) use ($origin) {
            if ($key == 'Access-Control-Allow-Origin') {
                header("{$key}:{$origin}");
            } else {
                if (is_array($value)) {
                    $value = implode(',', $value);
                }
                header("{$key}:{$value}");
            }
        });
    }
    unset($origin, $crossDomainConfig);

    if (strtolower($_SERVER['REQUEST_METHOD']) == 'options') {
        exit();
    }
}
