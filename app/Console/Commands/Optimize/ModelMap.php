<?php

namespace App\Console\Commands\Optimize;

use App\Utils\Tools;
use Illuminate\Console\Command;
use function app_path;

class ModelMap extends Command
{
    protected $signature = 'optimize:model-map';

    protected $description = '生成模型类映射';
    protected $base_controller_name = 'BaseController';

    public function handle()
    {
        $model_list = Tools::scanFile(app_path('Models'));
        $result = $this->makeDocProperty($model_list, '\\App\\Models');
        file_put_contents(config_path('model_map.php'), "<?php   \nreturn " . var_export($result['alias'], true) . ';');
        $BaseControllerDocBegin = '/**' . PHP_EOL . ' * 控制器类';
        $BaseControllerDocEnd = ' * Class Controller' . PHP_EOL . ' */';
        $BaseControllerDoc = array_merge([
            $BaseControllerDocBegin,
        ], $result['doc'], [
            $BaseControllerDocEnd,
        ]);
        $BaseControllerFilePath = app_path('Http/Controllers/' . $this->base_controller_name . '.php');
        $BaseControllerContent = file_get_contents($BaseControllerFilePath);
        file_put_contents($BaseControllerFilePath, preg_replace('/(\/\*\*[^\/]+\/)?\sclass ' . $this->base_controller_name . '/', implode(PHP_EOL, $BaseControllerDoc) . PHP_EOL . 'class ' . $this->base_controller_name, $BaseControllerContent));
        // 指令输出
        $this->info('生成模型类映射完成！');
        return 0;
    }

    public function makeDocProperty($list, $namespace)
    {
        $result = ['doc' => [], 'alias' => []];
        foreach ($list as $key => $item) {
            if (is_array($item)) {
                $child_result = $this->makeDocProperty($item, "{$namespace}\\{$key}");
                $result['doc'] = array_merge($result['doc'], $child_result['doc']);
                $result['alias'] = array_merge($result['alias'], $child_result['alias']);
            } else {
                if (strpos($item, '.php') === false) continue;
                $modelClassName = str_replace('.php', '', $item);
                if (strtolower($modelClassName) == 'model') continue;
                // $modelAlias = "model{$modelClassName}";
                $modelAlias = 'model' . implode('', explode('\\', str_replace('\\App\\Models', '', $namespace))) . $modelClassName;
                $modelClass = "{$namespace}\\{$modelClassName}";
                $result['alias'][$modelAlias] = $modelClass;
                $result['doc'][] = " * @property {$modelClass} \${$modelAlias} {$modelClassName}";
            }
        }
        return $result;
    }

}
