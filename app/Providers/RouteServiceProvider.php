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

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * 为应用程序配置速率限制器。
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        /*RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });*/
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
