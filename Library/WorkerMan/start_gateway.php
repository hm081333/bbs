<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

use GatewayWorker\Gateway;
use Workerman\Protocols\Websocket;
use Workerman\Worker;

if (!defined('GLOBAL_START')) {
    defined('API_ROOT') || define('API_ROOT', dirname(dirname(dirname(__FILE__))));

    require_once API_ROOT . '/public/init.php';
}

// gateway 进程
$gateway = new Gateway("Websocket://127.0.0.1:7272");
// 设置名称，方便status时查看
$gateway->name = 'Gateway';
// 设置进程数，gateway进程数建议与cpu核数相同
$gateway->count = 4;
// 分布式部署时请设置成内网ip（非127.0.0.1）
$gateway->lanIp = '127.0.0.1';
// 内部通讯起始端口。假如$gateway->count=4，起始端口为2300
// 则一般会使用2300 2301 2302 2303 4个端口作为内部通讯端口
$gateway->startPort = 2300;
// 心跳间隔
$gateway->pingInterval = 10;
// 允许心跳无响应次数
$gateway->pingNotResponseLimit = 5;
// 心跳数据
// $gateway->pingData = '{"type":"ping"}';
// $gateway->pingData = gzencode('{"type":"ping"}');
$gateway->pingData = \Common\gzip_binary_string_encode('{"type":"ping"}');
// 服务注册地址
$gateway->registerAddress = '127.0.0.1:1236';


// 当客户端连接上来时，设置连接的onWebSocketConnect，即在websocket握手时的回调
$gateway->onConnect = function ($connection) use ($gateway) {
    // $connection->websocketType = Websocket::BINARY_TYPE_ARRAYBUFFER;
    $connection->onWebSocketConnect = function ($connection, $http_header) use ($gateway) {
        // 可以在这里判断连接来源是否合法，不合法就关掉连接
        // $_SERVER['HTTP_ORIGIN']标识来自哪个站点的页面发起的websocket链接
        if (!in_array(($_SERVER['HTTP_ORIGIN'] ?? ''), [
            'http://localhost:8080',
            'http://127.0.0.1:8080',
            'http://10.0.0.20:8080',
            'http://192.168.1.135:8080',
            'http://bbs2.lyihe2.tk',
            'https://bbs2.lyihe2.tk',
            'http://bbs2-ws.lyihe2.tk',
            'https://bbs2-ws.lyihe2.tk',
        ])) {
            $connection->close();
        }
        // onWebSocketConnect 里面$_GET $_SERVER是可用的
        // var_dump($_GET, $_SERVER);
        // 重写后需要调起原方法，不然没有 onWebSocketConnect 通知
        $gateway->onWebsocketConnect($connection, $http_header);
    };
};


// 如果不是在根目录启动，则运行runAll方法
if (!defined('GLOBAL_START')) {
    Worker::runAll();
}
