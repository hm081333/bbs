<?php

namespace App\Providers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * 注册任何应用程序服务。
     */
    public function register(): void
    {
        //region 已自动发现加载，无需以下代码
        // if (!Tools::isProduction() && Tools::isDebug() && class_exists(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class)) {
        //     $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        // }
        //endregion

        $this->app->singleton(\App\Utils\Register\JWTAuth::class, function ($app) {
            return new \App\Utils\Register\JWTAuth;
        });
    }

    /**
     * 获取服务提供者的服务
     *
     * @return array
     */
    public function provides(): array
    {
        return [\App\Utils\Register\JWTAuth::class];
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Response::macro('api', function (string|null $msg, mixed $data, array $extend = []): JsonResponse {
            return new JsonResponse(array_merge([
                'code' => 200,
                'msg' => $msg,
                'data' => $data,
            ], $extend));
        });
    }
}
