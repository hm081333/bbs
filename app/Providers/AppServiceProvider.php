<?php

namespace App\Providers;

use App\Utils\Tools;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //region 已自动发现加载，无需以下代码
        // if (!Tools::isProduction() && Tools::isDebug() && class_exists(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class)) {
        //     $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        // }
        //endregion
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('api', function (string|null $msg, mixed $data) {
            return Response::json([
                'code' => 200,
                'msg' => $msg,
                'data' => $data,
            ]);
        });
    }
}
