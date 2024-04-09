<?php

namespace App\Providers;

use App\Mixins\BlueprintMixin;
use App\Mixins\BuilderMixin;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;
use MongoDB\Laravel\Eloquent\Model;

class ModelServiceProvider extends ServiceProvider implements \Illuminate\Contracts\Support\DeferrableProvider
{
    /**
     * 注册所有的应用服务
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(\App\Utils\Register\ModelMap::class, function ($app) {
            return new \App\Utils\Register\ModelMap;
        });
    }

    /**
     * 获取服务提供者的服务
     *
     * @return array
     */
    public function provides(): array
    {
        return [\App\Utils\Register\ModelMap::class];
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Builder::mixin(new BuilderMixin());
        Blueprint::mixin(new BlueprintMixin());
    }

}
