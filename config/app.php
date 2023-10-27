<?php

use Illuminate\Support\Facades\Facade;

return [

    /*
    |--------------------------------------------------------------------------
    | 应用名称
    |--------------------------------------------------------------------------
    |
    | 该值是您的应用程序的名称。当框架需要将应用程序的名称放置在通知或应用程序或其包所需的任何其他位置时，使用此值。
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | 应用环境
    |--------------------------------------------------------------------------
    |
    | 该值确定您的应用程序当前运行的“环境”。这可能决定您更喜欢如何配置应用程序使用的各种服务。在“.env”文件中设置它。
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | 应用程序调试模式
    |--------------------------------------------------------------------------
    |
    | 当您的应用程序处于调试模式时，应用程序中发生的每个错误都会显示带有堆栈跟踪的详细错误消息。如果禁用，则会显示一个简单的通用错误页面。
    |
    */

    'debug' => (bool)env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | 应用程序的URL
    |--------------------------------------------------------------------------
    |
    | 当使用Artisan命令行工具
    | 时，控制台使用这个URL来正确地生成URL。您应该将此设置为应用程序
    | 的根目录，以便在运行Artisan任务时使用它。
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    'asset_url' => env('ASSET_URL', null),

    'slow_query_log' => true,

    /*
    |--------------------------------------------------------------------------
    | 应用程序时区
    |--------------------------------------------------------------------------
    |
    | 您可以在此处指定应用程序的默认时区，PHP 日期和日期时间函数将使用该时区。我们已经将其设置为开箱即用的合理默认值。
    |
    */

    'timezone' => 'Asia/Shanghai',

    /*
    |--------------------------------------------------------------------------
    | 应用程序区域设置配置
    |--------------------------------------------------------------------------
    |
    | 应用程序区域设置确定翻译服务提供商将使用的默认区域设置。您可以随意将此值设置为应用程序支持的任何区域设置。
    |
    */

    'locale' => 'zh_CN',

    /*
    |--------------------------------------------------------------------------
    | 应用程序回退区域设置
    |--------------------------------------------------------------------------
    |
    | 后备区域设置确定当前区域设置不可用时要使用的区域设置。您可以更改该值以对应于通过应用程序提供的任何语言文件夹。
    |
    */

    'fallback_locale' => 'zh_CN',

    /*
    |--------------------------------------------------------------------------
    | Faker 区域设置
    |--------------------------------------------------------------------------
    |
    | 当为数据库种子生成虚假数据时，Faker PHP 库将使用此区域设置。例如，这将用于获取本地化电话号码、街道地址信息等。
    |
    */

    'faker_locale' => 'zh_CN',

    /*
    |--------------------------------------------------------------------------
    | 加密密钥
    |--------------------------------------------------------------------------
    |
    | 该密钥由 Illuminate 加密器服务使用，应设置为随机的 32 个字符的字符串，否则这些加密的字符串将不安全。请在部署应用程序之前执行此操作！
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
   |--------------------------------------------------------------------------
   | 自动加载服务提供商
   |--------------------------------------------------------------------------
   |
   | 此处列出的服务提供商将根据您的应用程序的请求自动加载。请随意将您自己的服务添加到此阵列中，以为您的应用程序授予扩展功能。
   |
   */

    'providers' => [
        /*
         * Laravel 框架服务提供商...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        /*
         * 套餐服务提供商...
         */
        //\Tymon\JWTAuth\Providers\LaravelServiceProvider::class,// jwt-auth
        //\Overtrue\LaravelWeChat\ServiceProvider::class,
        //\BeyondCode\LaravelWebSockets\WebSocketsServiceProvider::class,

        /*
         * 应用服务提供商...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        App\Providers\ModelServiceProvider::class,

    ],

    /*
    |--------------------------------------------------------------------------
    | 类别名
    |--------------------------------------------------------------------------
    |
    | 该类别名数组将在应用程序启动时注册。但是，您可以随意注册任意数量的别名，因为别名是“延迟”加载的，因此不会影响性能。
    |
    */

    'aliases' => Facade::defaultAliases()->merge([
        //'JWTAuth' => Tymon\JWTAuth\Facades\JWTAuth::class,
        'EasyWeChat' => Overtrue\LaravelWeChat\EasyWeChat::class,
    ])->toArray(),

];
