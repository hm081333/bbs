<?php

namespace App\Console\Commands\Optimize;

use App\Models\AuthModel;
use App\Models\BaseModel;
use App\Utils\Tools;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use function app_path;

class ModelColumnMap extends Command
{
    protected $signature = 'optimize:model-column-map';

    protected $description = '生成模型类字段映射';

    public function handle()
    {
        $model_map = $this->getModelMapList(Tools::scanFile(app_path('Models')), '\\App\\Models');
        $model_column_map = [];
        foreach ($model_map as $modelAlias => $modelClass) {
            /* @var $modelInstance \Illuminate\Database\Eloquent\Model */
            $modelInstance = new $modelClass;
            $table_name_without_prefix = $modelInstance->getTable();
            $table_columns = [];
            foreach (Schema::getColumnListing($table_name_without_prefix) as $column_name) {
                $table_columns[$column_name] = $this->identifyColumnType($column_name, Schema::getColumnType($table_name_without_prefix, $column_name));
            }
            $model_column_map[$modelAlias] = $table_columns;
        }
        file_put_contents(config_path('model_column_map.php'), "<?php   \nreturn " . var_export($model_column_map, true) . ';');
        // 指令输出
        $this->info('生成模型类字段映射完成！');
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
                if (in_array(ltrim($modelClass, '\\'), [
                    BaseModel::class,
                    AuthModel::class,
                ])) continue;
                $modelAlias = Tools::modelAlias($modelClass, 'model');
                $model_list[$modelAlias] = $modelClass;
            }
        }
        return $model_list;
    }

    private function identifyColumnType($column_name, $column_type)
    {
        // if ($column_type == 'bigint') return 'integer';
        return $column_type;
        //region 时间类型
        if (str_ends_with($column_name, '_at') || str_ends_with($column_name, '_time')) {
            dd($column_name, $column_type);
        }
        //endregion
    }

}
