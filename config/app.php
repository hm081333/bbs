<?php
// +----------------------------------------------------------------------
// | 应用设置
// +----------------------------------------------------------------------

return [
    // 应用地址
    'app_host' => env('app.host', ''),
    'cdn_root' => env('app.cdn_root'),
    // 应用的命名空间
    'app_namespace' => '',
    // 是否启用路由
    'with_route' => true,
    // 默认应用
    'default_app' => 'index',
    // 默认时区
    'default_timezone' => 'Asia/Shanghai',

    // 应用映射（自动多应用模式有效）
    'app_map' => [],
    // 域名绑定（自动多应用模式有效）
    'domain_bind' => [],
    // 禁止URL访问的应用列表（自动多应用模式有效）
    'deny_app_list' => [],

    // 异常页面的模板文件
    'exception_tmpl' => app()->getThinkPath() . 'tpl/think_exception.tpl',

    // 错误显示信息,非调试模式有效
    'error_message' => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg' => true,

    'session_name' => '9cf3eeaffa0c8386508f932a354adf70',
    'admin_token' => '1858d7d84c1f48cd63eda4f989dbf9e7',
    'user_token' => '85cc39d65ded51e8ffb949503e83ed65',
    'post_key' => 'abac49e1f519fe724f10e4fb40cf0a38',
];
