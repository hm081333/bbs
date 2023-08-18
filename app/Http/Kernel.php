<?php

namespace App\Http;

use App\Http\Middleware\AccessLog;
use App\Http\Middleware\Auth\UserAuth;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\DBTransaction;
use App\Http\Middleware\EncryptCookies;
use App\Http\Middleware\OPCacheRequest;
use App\Http\Middleware\PreventRequestsDuringMaintenance;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\TrimStrings;
use App\Http\Middleware\TrustProxies;
use App\Http\Middleware\VerifyCsrfToken;
use Fruitcake\Cors\HandleCors;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Auth\Middleware\RequirePassword;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Routing\Middleware\ThrottleRequestsWithRedis;
use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

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
        //TrustHosts::class,// 信任主机
        //TrustProxies::class,// 信任代理
        //\Illuminate\Http\Middleware\HandleCors::class,// 跨域请求
        \App\Http\Middleware\HandleCors::class,// 跨域请求
        PreventRequestsDuringMaintenance::class,// 维护模式相关
        ValidatePostSize::class,// 验证 POST 数据大小
        TrimStrings::class,// 清理请求内容前后空白字符
        ConvertEmptyStringsToNull::class,// 空字符转成 null
        //AccessLog::class,// 访问日志
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'opcache' => [
            OPCacheRequest::class,
        ],
        'web' => [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
        ],
        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',// API请求频率限制，具体次数限制前往\App\Providers\RouteServiceProvider修改configureRateLimiting
            //\Illuminate\Routing\Middleware\SubstituteBindings::class,
            DBTransaction::class,// 数据库事务
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        // 用户端
        'auth.user' => UserAuth::class,
        // 使用速率限制
        //'throttle' => ThrottleRequests::class,
        // 使用 Redis 管理速率限制
        'throttle' => ThrottleRequestsWithRedis::class,
        //'auth' => Authenticate::class,
        //'auth.basic' => AuthenticateWithBasicAuth::class,
        //'cache.headers' => SetCacheHeaders::class,
        //'can' => Authorize::class,
        //'guest' => RedirectIfAuthenticated::class,
        //'password.confirm' => RequirePassword::class,
        //'signed' => ValidateSignature::class,
        //'throttle' => ThrottleRequests::class,
        //'verified' => EnsureEmailIsVerified::class,
    ];
}
