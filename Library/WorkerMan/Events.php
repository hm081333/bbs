<?php
/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);

/**
 * WebSocket主逻辑
 * 主要是处理 onMessage onClose
 */

use \GatewayWorker\Lib\Gateway;
use PhalApi\PhalApi;
use PhalApi\Request;
use function Common\DI;

class Events
{
    /**
     * 服务启动时
     * @param $obj
     */
    public static function onWorkerStart($obj)
    {
        // var_dump('onWorkerStart', $obj);
        self::sessionSaveHandler()->clearSessionId();
    }

    /**
     * 客户端请求连接时
     * @param $client_id
     */
    public static function onConnect($client_id)
    {
        // var_dump('onConnect', $client_id);
    }

    /**
     * 客户端连接成功时
     * @param string $client_id
     * @param array  $socket_nfo
     */
    public static function onWebSocketConnect(string $client_id, array $socket_nfo)
    {
        // var_dump('onWebSocketConnect', $client_id, $socket_nfo);
        $get = $socket_nfo['get'];
        $server = $socket_nfo['server'];
        DI()->cache->set('ws_server:' . $client_id, $server);
        $_COOKIE = $socket_nfo['cookie'];
        // var_dump($get);
        // var_dump($server);
        // var_dump($_COOKIE);

        if (isset($_COOKIE[SESSION_NAME])) {
            $session_id = $_COOKIE[SESSION_NAME];
        } else {
            $session_id = self::sessionSaveHandler()->getSessionId($client_id) ?? md5(uniqid());
            self::sendToClient($client_id, ['type' => 'init', 'cookie' => [SESSION_NAME => $session_id]]);
        }
        DI()->logger->debug('websocket session id', $session_id);
        self::sessionSaveHandler()->setSessionId($client_id, $session_id);

    }

    /**
     * Session存储
     * @return \Library\Session\Redis
     */
    public static function sessionSaveHandler()
    {
        $session_set_save_handler = new \Library\Session\Redis();
        $session_set_save_handler->open('', SESSION_NAME);
        // 初始化Redis存储方式
        return $session_set_save_handler;
    }

    /**
     * 读取session
     * @param string $session_id
     * @return mixed
     */
    public static function getSession(string $session_id)
    {
        // 读取该session_id对应的数据
        $session_data = self::sessionSaveHandler()->read($session_id);
        return empty($session_data) ? [] : (DI()->serialize->decrypt($session_data) ?: []);
    }

    /**
     * 保存session
     * @param string $session_id
     * @param array  $session
     * @return bool
     */
    public static function saveSession(string $session_id, array $session)
    {
        return self::sessionSaveHandler()->write($session_id, DI()->serialize->encrypt($session));
    }

    /**
     * 有消息时
     * @param string $client_id
     * @param string $message
     * @return void
     * @throws Exception
     */
    public static function onMessage($client_id, $message)
    {
        DI()->logger->debug("收到消息|client_id|{$client_id}|message|{$message}");
        // 解析接收到的消息
        $data = json_decode($message, true);
        // 请求类型 没有请求类型时返回心跳
        $dataType = $data['type'] ?? 'ping';
        // 请求参数
        $request = $data['request'] ?? [];
        // 默认响应数据
        $response = ['type' => $dataType, 'requestId' => md5(rawurlencode($message)), 'response' => []];
        switch ($dataType) {
            // 回应心跳
            case 'pong':
                return;
                break;
            // 请求接口
            case 'api':
                // 访问服务参数
                // $request['s'] = $request['s'] ?? 'Common.Base.Index';
                $request['s'] = $request['s'] ?? '';
                // 拆解访问参数
                $service = explode('.', $request['s']);
                // api地址格式是否正确
                if (count($service) != 3) {
                    $apiResult = [
                        'ret' => 400,
                        'data' => '',
                        'msg' => '接口地址有误，请检查后尝试重新请求！',
                    ];
                    break;
                } else {
                    foreach (explode('|', urldecode($data['Auth'])) as $item) {
                        $key = substr($item, 0, strlen(USER_TOKEN));
                        $value = substr($item, strlen(USER_TOKEN));
                        if ($key == ADMIN_TOKEN) {
                            \Common\Domain\Admin::$admin_token = $value;
                        } else if ($key == USER_TOKEN) {
                            \Common\Domain\User::$user_token = $value;
                        }
                    }

                    $session_id = self::sessionSaveHandler()->getSessionId($client_id);
                    $_SESSION = self::getSession($session_id);
                    $server = DI()->cache->get('ws_server:' . $client_id) ?? [];
                    $_SERVER = array_merge(($_SERVER ?? []), $server);
                    DI()->cache->expire('ws_server:' . $client_id);

                    // var_dump("session_id|{$session_id}|session", $_SESSION);

                    // 清空上次请求结果数据
                    DI()->response->setRet(200)->setMsg('')->setData([]);
                    // 响应操作类
                    $pai = new PhalApi();
                    // 重新建立api请求
                    DI()->request = new Request($request);
                    // 获取api返回结果
                    // $apiResult = DI()->pai->response()->getResult();
                    $apiResult = $pai->response()->getResult();
                    self::saveSession($session_id, $_SESSION ?? []);
                    // var_dump("session_id|{$session_id}|session", $_SESSION, '----------');
                }
                $response['response'] = $apiResult;
                break;
            default:
                break;
        }
        DI()->logger->info("响应消息|client_id|{$client_id}|response", $response);
        // 下发响应数据到客户端
        self::sendToClient($client_id, $response);
    }
    // Gateway::sendToGroup($room_id, json_encode($new_message));
    // Gateway::joinGroup($client_id, $room_id);
    // Gateway::sendToCurrentClient(json_encode($new_message));
    // Gateway::sendToClient($message_data['to_client_id'], json_encode($new_message));

    /**
     * 下发消息到客户端
     * @param string       $client_id
     * @param string|array $data
     * @return void
     */
    public static function sendToClient($client_id, $data)
    {
        $data = is_array($data) ? json_encode($data, true) : $data;
        Gateway::sendToClient($client_id, $data);
    }

    /**
     * 当客户端断开连接时
     * @param string $client_id 客户端id
     */
    public static function onClose($client_id)
    {
        // debug
        var_dump('onClose', "client:{$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']} gateway:{$_SERVER['GATEWAY_ADDR']}:{$_SERVER['GATEWAY_PORT']}  client_id:$client_id onClose:''");
        self::sessionSaveHandler()->delSessionId($client_id);

        // 从房间的客户端列表中删除
        // if (isset($_SESSION['room_id'])) {
        //     $room_id = $_SESSION['room_id'];
        //     $new_message = ['type' => 'logout', 'from_client_id' => $client_id, 'from_client_name' => $_SESSION['client_name'], 'time' => date('Y-m-d H:i:s')];
        //     Gateway::sendToGroup($room_id, json_encode($new_message));
        // }
    }

}
