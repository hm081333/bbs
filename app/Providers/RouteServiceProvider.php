<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;
use Tymon\JWTAuth\Facades\JWTAuth;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::prefix('opcache')
                ->middleware('opcache')
                ->namespace($this->namespace)
                ->name('opcache.')
                ->group(base_path('routes/opcache.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));

            // Route::fallback(function (Request $request) {
            //     return response()->view('errors.404')->setStatusCode(404);
            // });

            // Route::any('{fallbackPlaceholder}', function (Request $request) {
            //     if ($request->isJson() || $request->expectsJson()) {
            //         return response()->json('404 Not Found')->setStatusCode(404);
            //     } else {
            //         return response()->view('errors.404')->setStatusCode(404);
            //     }
            // })->where('fallbackPlaceholder', '.*')->fallback();
        });
    }

    /**
     * 为应用程序配置速率限制器。
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            $key = 'ip:' . $request->ip();
            $token_parser = app()->make('tymon.jwt.parser');
            $token_str = $token_parser->parseToken();
            if ($token_str) {
                try {
                    $JWTParser = new Parser(new JoseEncoder());
                    $token = $JWTParser->parse($token_str);
                    $claims = $token->claims();
                    $account_type = $claims->get('account_type', '');
                    $account_id = $claims->get('sub', 0);
                    if (!empty($account_type) && !empty($account_id)) $key = "{$account_type}:{$account_id}";
                } catch (\Exception $e) {
                    Log::error('获取令牌信息失败');
                    Log::error($token_str);
                    Log::error($e);
                }
            }
            return Limit::perMinute(60)->by($key);
        });
    }
}
