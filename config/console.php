<?php
// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------
return [
    // 指令定义
    'commands' => [
        'task' => \library\exception\command\Task::class,
        'optimize:model' => \library\exception\command\optimize\Model::class
    ],
];
