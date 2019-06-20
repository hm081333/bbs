<?php

namespace Task\Runner;

use function Common\DI;
use PhalApi\Exception\InternalServerErrorException;
use PhalApi\Task\Runner;
use PhalApi\Request;
use PhalApi\Response\JsonResponse;
use PhalApi\PhalApi;

/**
 * 本地调度器 LocalRunner
 *
 * - 本地内部调度
 * - 不能在Api请求时进行此调度
 *
 * @author dogstar <chanzonghuang@gmail.com> 20150516
 */
class LocalRunner extends Runner
{

    /**
     * 执行任务
     * @param string $service MQ中的接口服务名称，如：Site.Index
     * @return array('total' => 总数量, 'fail' => 失败数量)
     * @throws \Exception
     */
    public function go($service)
    {
        $rs = ['total' => 0, 'success' => 0, 'fail' => 0];
        if (is_array($service)) {
            $params = $service;
            $service = $params['s'];
            unset($params['s']);
            $rs['total'] = 1;

            try {
                $isFinish = $this->youGo($service, $params);
                if (!$isFinish) {
                    $rs['fail']++;
                    $failList[] = $params;
                } else {
                    $rs['success']++;
                }
            } catch (InternalServerErrorException $ex) {
                $rs['fail']++;
                $failList[] = $params;

                DI()->logger->error('task occur exception to go',
                    ['service' => $service, 'params' => $params, 'error' => $ex->getMessage()]);
            }
        } else {

            $todoList = $this->mq->pop($service, $this->step);
            $failList = [];

            while (!empty($todoList)) {
                $rs['total'] += count($todoList);

                foreach ($todoList as $params) {
                    try {
                        $isFinish = $this->youGo($service, $params);
                        if (!$isFinish) {
                            $rs['fail']++;
                            $failList[] = $params;
                        } else {
                            $rs['success']++;
                        }
                    } catch (InternalServerErrorException $ex) {
                        $rs['fail']++;
                        $failList[] = $params;

                        DI()->logger->error('task occur exception to go',
                            ['service' => $service, 'params' => $params, 'error' => $ex->getMessage()]);
                    }
                }

                $todoList = $this->mq->pop($service, $this->step);
            }

            foreach ($failList as $params) {
                $this->mq->add($service, $params);
            }
        }

        return $rs;
    }

    /**
     * 具体的执行，这里使用了一个彩蛋的命名
     * @param string $service MQ中的接口服务名称，如：Site.Index
     * @param array  $params  参数
     * @return boolean 成功返回TRUE，失败返回FALSE
     * @throws \Exception
     */
    protected function youGo($service, $params)
    {
        $params['service'] = $service;

        DI()->request = new Request($params);
        DI()->response = new JsonResponse();

        $phalapi = new PhalApi();
        $rs = $phalapi->response();
        $apiRs = $rs->getResult();

        if ($apiRs['ret'] != 200) {
            DI()->logger->debug('task local go fail',
                ['servcie' => $service, 'params' => $params, 'rs' => $apiRs]);

            return false;
        }

        return true;
    }

}

