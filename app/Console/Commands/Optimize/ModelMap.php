<?php

namespace App\Console\Commands\Optimize;

use App\Models\AuthModel;
use App\Models\BaseModel;
use App\Utils\Tools;
use Illuminate\Console\Command;
use function app_path;

class ModelMap extends Command
{
    protected $signature = 'optimize:model-map';

    protected $description = '生成模型类映射';

    public function handle()
    {
        $model_map = $this->getModelMapList(Tools::scanFile(app_path('Models')), '\\App\\Models');
        file_put_contents(config_path('model_map.php'), "<?php   \nreturn " . var_export($model_map, true) . ';');

        $BaseControllerDoc = '/**' . PHP_EOL . ' * 模型映射类' . PHP_EOL . ' *' . PHP_EOL;
        array_walk($model_map, function ($modelClass, $modelAlias) use (&$BaseControllerDoc) {
            $BaseControllerDoc .= " * @property {$modelClass} \${$modelAlias} " . class_basename($modelClass) . PHP_EOL;
        });
        $BaseControllerDoc .= ' * Class ModelMap' . PHP_EOL . ' */';

        $BaseControllerFilePath = app_path('Utils/Register/ModelMap.php');
        $BaseControllerContent = file_get_contents($BaseControllerFilePath);

        file_put_contents($BaseControllerFilePath, preg_replace('/(\/\*\*[^\/]+\/)?\s*class ModelMap/', $BaseControllerDoc . PHP_EOL . 'class ModelMap', $BaseControllerContent));


        // 指令输出
        $this->info('生成模型类映射完成！');
        return 0;
    }

    /**
     * 获取模型映射列表
     *
     * @param array  $list      模型目录下文件列表
     * @param string $namespace 当前模型目录对应的命名空间
     *
     * @return array
     */
    private function getModelMapList(array $list, string $namespace): array
    {
        $model_list = [];
        foreach ($list as $key => $item) {
            if (is_array($item)) {
                $child_model_list = $this->getModelMapList($item, "{$namespace}\\{$key}");
                $model_list = array_merge($model_list, $child_model_list);
            } else {
                if (!str_ends_with($item, '.php')) continue;
                $modelClassName = str_replace('.php', '', $item);
                $modelClass = "{$namespace}\\{$modelClassName}";
                if (!class_exists($modelClass)) continue;
                // if (in_array(ltrim($modelClass, '\\'), [
                //     BaseModel::class,
                //     AuthModel::class,
                // ])) continue;
                $modelAlias = Tools::modelAlias($modelClass, 'model');
                $model_list[$modelAlias] = $modelClass;
            }
        }
        return $model_list;
    }

}
