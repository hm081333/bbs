<?php
// 全局中间件定义文件
return [
    // 全局请求缓存
    // \think\middleware\CheckRequestCache::class,
    // 多语言加载
    // \think\middleware\LoadLangPack::class,
    // pathinfo初始化
    \app\middleware\PathInfoInit::class,
    // Session初始化
    \think\middleware\SessionInit::class,
    // 跨域请求支持
    \think\middleware\AllowCrossDomain::class,
    // 请求令牌支持
    // \app\middleware\Auth::class,
];
