<?php

namespace App\Console\Commands\Create;

use Illuminate\Console\Command;

class Api extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:api {api}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成接口';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $api = $this->argument('api');
        [$namespace, $controller] = explode('/', $api);
        $route_file_path = base_path('routes/apis/' . strtolower($namespace) . '.php');
        $controller_tpl_file_path = resource_path('base/controller.tpl');
        //region 合成并保存路由
        $route_content = file_get_contents($route_file_path);
        $route_content .= "Route::prefix('" . strtolower($controller) . "')->group(function () {
    Route::post('add', [{$controller}Controller::class, 'add'])->name('" . strtolower($namespace) . "." . strtolower($controller) . ".add');
    Route::post('edit', [{$controller}Controller::class, 'edit'])->name('" . strtolower($namespace) . "." . strtolower($controller) . ".edit');
    Route::post('list', [{$controller}Controller::class, 'list'])->name('" . strtolower($namespace) . "." . strtolower($controller) . ".list');
    Route::post('info', [{$controller}Controller::class, 'info'])->name('" . strtolower($namespace) . "." . strtolower($controller) . ".info');
    Route::post('del', [{$controller}Controller::class, 'del'])->name('" . strtolower($namespace) . "." . strtolower($controller) . ".del');
});";
        file_put_contents($route_file_path, $route_content);
        //endregion
        //region 合成并保存控制器
        $controller_content = file_get_contents($controller_tpl_file_path);
        $controller_content = str_replace(['{{namespace}}', '{{controller}}'], [$namespace, $controller], $controller_content);
        file_put_contents(app_path('Http/Controllers/' . $namespace . "/{$controller}Controller.php"), $controller_content);
        //endregion
        // 指令输出
        $this->info('生成接口完成！');
        return 0;
    }
}
