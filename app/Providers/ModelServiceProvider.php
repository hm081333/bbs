<?php

namespace App\Providers;

use App\Mixins\BlueprintMixin;
use App\Mixins\BuilderMixin;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class ModelServiceProvider extends ServiceProvider
{
    /**
     * 注册所有的应用服务
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(\App\Utils\Register\ModelMap::class, function ($app) {
            return new \App\Utils\Register\ModelMap();
        });
    }

    /**
     * 获取服务提供者的服务
     *
     * @return array
     */
    public function provides()
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
        /*Request::macro('isVip', function(){
            if(auth()->check())
            {
                return $this->user()->is_vip;
            }
            return false;
        });*/
    }

}
