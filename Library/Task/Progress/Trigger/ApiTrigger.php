<?php

namespace Library\Task\Progress\Trigger;

use Library\Exception\InternalServerErrorException;
use PhalApi\Task\Progress\Trigger;
use function PhalApi\T;

/**
 * API 触发器接口
 */
class ApiTrigger implements Trigger
{
    /**
     * 进程的具体操作
     * @param string $params 对应数据库表task_progress.fire_params字段
     * @return mixed
     * @throws InternalServerErrorException
     */
    public function fire($params)
    {
        $paramsArr = json_decode($params, true);
        if (!$paramsArr) {
            $paramsArr = explode('&', $params);
        }

        if (empty($paramsArr['s'])) {
            throw new InternalServerErrorException(T('缺少接口参数'));
        }

        $service = trim($paramsArr['s']);
        $mqClass = !empty($paramsArr['mq']) ? trim($paramsArr['mq']) : 'PhalApi\Task\MQ\RedisMQ';// 默认使用Redis
        $runnerClass = !empty($paramsArr['runner']) ? trim($paramsArr['runner']) : 'Task\Runner\LocalRunner';// 默认执行器
        unset($paramsArr['mq'], $paramsArr['runner']);

        $mq = new $mqClass();
        $runner = $this->getRunner($runnerClass, $mq);

        return $runner->go($paramsArr);
    }

    /**
     * @param $runnerClass
     * @param $mq
     * @return \PhalApi\Task\Runner
     * @return mixed
     */
    private function getRunner($runnerClass, $mq)
    {
        return new $runnerClass($mq);
    }
}
