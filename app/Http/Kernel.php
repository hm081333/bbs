<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,// 信任主机
        \App\Http\Middleware\TrustProxies::class,// 信任代理
        // \Illuminate\Http\Middleware\HandleCors::class,// 跨域请求
        \App\Http\Middleware\HandleCors::class,// 跨域请求
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,// 维护模式相关
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,// 验证 POST 数据大小
        \App\Http\Middleware\TrimStrings::class,// 清理请求内容前后空白字符
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,// 空字符转成 null
        //AccessLog::class,// 访问日志
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            // \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            // \Illuminate\Routing\Middleware\SubstituteBindings::class,

            'throttle:api',// API请求频率限制，具体次数限制前往\App\Providers\RouteServiceProvider修改configureRateLimiting
            \App\Http\Middleware\DBTransaction::class,// 数据库事务
        ],

        'opcache' => [
            \App\Http\Middleware\OPCacheRequest::class,
        ],
    ];

    /**
     * The application's middleware aliases.
     *
     * Aliases may be used instead of class names to conveniently assign middleware to routes and groups.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        // 使用速率限制
        // 'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        // 使用 Redis 管理速率限制
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequestsWithRedis::class,
        // 身份验证
        'auth' => \App\Http\Middleware\Authenticate::class,
        // 'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        // 'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        // 'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        // 'can' => \Illuminate\Auth\Middleware\Authorize::class,
        // 'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        // 'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        // 'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        // 'signed' => \App\Http\Middleware\ValidateSignature::class,
        // 'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
    ];
}
