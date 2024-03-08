<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GatewayWorker\BusinessWorker;
use GatewayWorker\Gateway;
use GatewayWorker\Register;
use Workerman\Worker;

class Workerman extends Command
{
    protected $signature = 'workerman {action} {--d}';
    protected $description = 'Start a Workerman server.';

    public function handle()
    {
        global $argv;
        $action = $this->argument('action');
        $argv[0] = 'workerman';
        $argv[1] = $action;
        $argv[2] = $this->option('d') ? '-d' : '';
        $this->start();
    }

    private function start()
    {
        $this->startGateWay();
        $this->startBusinessWorker();
        $this->startRegister();
        Worker::runAll();
    }

    /**
     * 启动处理程序
     * @return void
     */
    private function startBusinessWorker()
    {
        // bussinessWorker 进程
        $worker = new BusinessWorker();
        // worker名称
        $worker->name = 'BusinessWorker';
        // bussinessWorker进程数量
        $worker->count = 4;
        // 服务注册地址
        $worker->registerAddress = '127.0.0.1:1236';
        // 设置 \App\Workerman\Events 类来处理业务
        $worker->eventHandler = \App\Workerman\Events::class;
    }

    /**
     * 启动网关
     * @return void
     */
    private function startGateWay()
    {
        // gateway进程
        $gateway = new Gateway("websocket://0.0.0.0:8899");
        // gateway名称 status方便查看
        $gateway->name = 'Gateway';
        // 设置进程数，gateway进程数建议与cpu核数相同
        $gateway->count = 4;
        // 分布式部署时请设置成内网ip（非127.0.0.1）
        $gateway->lanIp = '127.0.0.1';
        // 内部通讯起始端口，如果$gateway->count = 4 起始端口为2300
        // 则一般会使用 2300，2301 2个端口作为内部通讯端口
        $gateway->startPort = 2300;
        // 心跳间隔
        $gateway->pingInterval = 30;
        // 允许心跳无响应次数
        // 客户端连续$pingNotResponseLimit次$pingInterval时间内不发送任何数据则断开链接，并触发onClose。
        // 我们这里使用的是服务端主动发送心跳所以设置为0
        $gateway->pingNotResponseLimit = 5;
        //心跳数据
        $gateway->pingData = '{"type":"ping"}';
        //服务注册地址
        $gateway->registerAddress = '127.0.0.1:1236';
        /*
        // 当客户端连接上来时，设置连接的onWebSocketConnect，即在websocket握手时的回调
        $gateway->onConnect = function ($connection) {
            $connection->onWebSocketConnect = function ($connection, $http_header) {
                // 可以在这里判断连接来源是否合法，不合法就关掉连接
                // $_SERVER['HTTP_ORIGIN']标识来自哪个站点的页面发起的websocket链接
                if ($_SERVER['HTTP_ORIGIN'] != 'http://chat.workerman.net') {
                    $connection->close();
                }
                // onWebSocketConnect 里面$_GET $_SERVER是可用的
                // var_dump($_GET, $_SERVER);
            };
        };
        */
    }

    /**
     * 启动服务注册
     * @return void
     */
    private function startRegister()
    {
        // register 服务必须是text协议
        new Register('text://127.0.0.1:1236');
    }
}
