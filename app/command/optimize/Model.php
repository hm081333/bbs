<?php
declare (strict_types=1);

namespace app\command\optimize;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class Model extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('生成模型类映射')
            ->setDescription('生成模型类映射');
    }

    protected function execute(Input $input, Output $output)
    {
        $BaseControllerDoc = '/**
 * 控制器基础类
';
        $model_alias = [];
        $model_list = scanFile(app_path('model'));
        foreach ($model_list as $item) {
            if (is_array($item) || strpos($item, '.php') === false) continue;
            $modelClassName = str_replace('.php', '', $item);
            $modelAlias = "model{$modelClassName}";
            $modelClass = "\\app\\model\\{$modelClassName}";
            $model_alias[$modelAlias] = $modelClass;
            $BaseControllerDoc .= " * @property {$modelClass} \${$modelAlias} {$modelClassName}" . PHP_EOL;
        }
        $BaseControllerDoc .= ' * Class BaseController
 * @package app
 */';
        file_put_contents(config_path() . 'model.php', "<?php   \nreturn " . var_export($model_alias, true) . ';');
        file_put_contents(runtime_path() . 'BaseControllerDoc.log', $BaseControllerDoc);
        // 指令输出
        $output->writeln('生成模型类映射完成！');
    }
}
