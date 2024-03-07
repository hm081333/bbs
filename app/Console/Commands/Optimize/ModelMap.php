<?php

namespace App\Console\Commands\Optimize;

use App\Models\AuthModel;
use App\Models\BaseModel;
use App\Utils\Tools;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use function app_path;

class ModelMap extends Command
{
    protected $signature = 'optimize:model-map';

    protected $description = '生成模型类映射';

    private $PHP_EOL = PHP_EOL;
    private $app_absolute_path = '';
    private $app_path = 'app';
    private $app_namespace = 'App';

    public function handle()
    {
        // 解决Doctrine\DBAL没有enum类型的问题
        \Illuminate\Support\Facades\DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', \Doctrine\DBAL\Types\Types::STRING);

        $this->info($this->description . '...');

        $this->app_absolute_path = app_path();

        $model_map = $this->getModelMapList(Tools::scanFile(app_path('Models')), '\\App\\Models');
        $this->saveModelMap($model_map);

        $properties = [];
        foreach ($model_map as $modelAlias => $modelInfo) {
            $properties[] = [
                'type' => $modelInfo['model'],
                'name' => $modelAlias,
                'desc' => ltrim($modelInfo['model'], '\\'),
            ];
        }

        $this->writeClassDoc('模型映射类', 'app/Http/Controllers/BaseController.php', $properties, 'model');
        $this->writeClassDoc('模型映射类', 'app/Utils/Register/ModelMap.php', $properties);

        // 指令输出
        $this->info($this->description . '完成！');
        return Command::SUCCESS;
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
                /* @var $modelInstance \Illuminate\Database\Eloquent\Model */
                $modelInstance = new $modelClass([], false);
                if (in_array($modelInstance::class, [
                    BaseModel::class,
                    AuthModel::class,
                ])) continue;
                $this->info($modelClass);// 打印当前处理模型类名
                // $modelAlias = Tools::modelAlias($modelClass, 'model');
                $modelAlias = Tools::modelAlias($modelInstance);
                $table_prefix = $modelInstance->getConnection()->getTablePrefix();
                $table_name_without_prefix = $modelInstance->getTable();
                $table_columns = [];
                foreach (Schema::getColumnListing($table_name_without_prefix) as $column_name) {
                    $table_columns[$column_name] = $this->identifyColumnType($column_name, Schema::getColumnType($table_name_without_prefix, $column_name));
                }
                $model_list[$modelAlias] = [
                    'model' => $modelClass,
                    'table' => $table_name_without_prefix,
                    'table_full_name' => $table_prefix . $table_name_without_prefix,
                    'column' => $table_columns,
                ];
            }
        }
        return $model_list;
    }

    /**
     * 保存模型映射
     *
     * @param array $model_map
     *
     * @return false|int
     */
    private function saveModelMap(array $model_map): bool|int
    {
        return file_put_contents(config_path('model_map.php'), "<?php   \nreturn " . var_export($model_map, true) . ';');
    }

    /**
     * 重定义字段类型
     *
     * @param string $column_name 字段名
     * @param string $column_type 字段类型
     *
     * @return string
     */
    private function identifyColumnType(string $column_name, string $column_type): string
    {
        // if ($column_type == 'bigint') return 'integer';
        return $column_type;
        //region 时间类型
        if (str_ends_with($column_name, '_at') || str_ends_with($column_name, '_time')) {
            dd($column_name, $column_type);
        }
        //endregion
    }

    private function writeClassDoc(string $name, string $path, array $properties, string $property_name_prefix = '')
    {
        $classFilePath = base_path($path);
        if (!file_exists($classFilePath)) {
            $this->error("文件不存在：{$path}");
            return false;
        }
        $path_info = pathinfo($path);
        $file_name = $path_info['basename'];
        $class_name = str_replace('.php', '', $file_name);
        $class_namespace = str_replace(DIRECTORY_SEPARATOR, '\\', str_replace($this->app_path, $this->app_namespace, $path_info['dirname']));

        $class_doc_str = "/**{$this->PHP_EOL} * {$name}{$this->PHP_EOL} *{$this->PHP_EOL}";
        // @property [类型] [名称] [描述]s
        $class_doc_str = array_reduce($properties, fn($class_doc_str, $property) => $class_doc_str . " * @property-read {$property['type']} {$property_name_prefix}{$property['name']} {$property['desc']}{$this->PHP_EOL}", $class_doc_str);
        $class_doc_str .= " * @package {$class_namespace}{$this->PHP_EOL} * @class {$class_name}{$this->PHP_EOL} */";

        file_put_contents($classFilePath, preg_replace("/(\/\*\*[^\/]+\/)?\s*class {$class_name}/", "{$class_doc_str}{$this->PHP_EOL}class {$class_name}", file_get_contents($classFilePath)));
    }

}
