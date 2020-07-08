<?php

namespace Library\WorkerMan;

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

use Common\Domain\Admin;
use Common\Domain\User;
use Exception;
use GatewayWorker\Lib\Gateway;
use Library\Request;
use Library\Session\Redis;
use PDOException;
use PhalApi\PhalApi;
use function Common\compress_binary_decode;
use function Common\compress_binary_encode;
use function Common\compress_string_decode;
use function Common\createDir;
use function Common\DI;

class Events
{
    static $maxBinaryBufferSize = 4096;

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
     * Session存储
     * @return Redis
     */
    public static function sessionSaveHandler()
    {
        $session_set_save_handler = new Redis();
        $session_set_save_handler->open('', SESSION_NAME);
        // 初始化Redis存储方式
        return $session_set_save_handler;
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
        DI()->cache->set('ws_server:' . $client_id, $server, 86400);
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
     * 下发消息到客户端
     * @param string       $client_id
     * @param string|array $data
     * @return void
     */
    public static function sendToClient($client_id, $data)
    {
        $data = is_array($data) ? json_encode($data, true) : $data;

        // gzip压缩
        // $data = gzencode($data);

        $data = compress_binary_encode($data, ZLIB_ENCODING_RAW);
        $data = self::parseData($client_id, $data);
        Gateway::sendToClient($client_id, $data);
    }

    /**
     * 格式化发送消息
     * @param $client_id
     * @param $data
     * @return false|string|string[]|null
     */
    public static function parseData($client_id, $data)
    {
        if (strlen($data) > self::$maxBinaryBufferSize) {
            // var_dump($data);
            $time = floor(microtime(true) * 1000);
            $times = ceil(strlen($data) / self::$maxBinaryBufferSize) - 1;
            $index = 0;
            $start = $index * self::$maxBinaryBufferSize;
            if ($index >= $times) {
                $sendType = 'end';
            } else {
                $sendType = 'sending';
            }
            self::writeLongMessageTemp($client_id, $time, $index, $data, 'send');

            $data = json_encode([
                'type' => 'receive',
                'sendType' => $sendType,
                'index' => $index,
                'time' => $time,
                'request' => base64_encode(substr($data, $start, self::$maxBinaryBufferSize)),
            ], true);

            return compress_binary_encode($data, ZLIB_ENCODING_RAW);
        }
        return $data;
    }

    /**
     * 发送长消息
     * @param $client_id
     * @param $time
     * @param $index
     */
    public static function sendLongMessage($client_id, $time, $index)
    {
        $request = self::readLongMessageTempWithIndex($client_id, $time, $index * self::$maxBinaryBufferSize, 'send');
        if (!$request || strlen($request) < self::$maxBinaryBufferSize) {
            $sendType = 'end';
            // self::delLongMessageTemp($client_id, $time, 'send');
        } else {
            $sendType = 'sending';
        }

        $data = json_encode([
            'type' => 'receive',
            'sendType' => $sendType,
            'index' => $index,
            'time' => $time,
            'request' => base64_encode($request ?: ''),
        ], true);

        Gateway::sendToClient($client_id, compress_binary_encode($data, ZLIB_ENCODING_RAW));
    }

    /**
     * 写入长消息的临时文件
     * @param        $client_id
     * @param        $micro_time
     * @param int    $index
     * @param string $data
     * @param string $type
     * @return bool
     */
    public static function writeLongMessageTemp($client_id, $micro_time, $index = 0, $data = '', $type = 'receive')
    {
        $temp_path = API_ROOT . "/runtime/temp/ws/{$type}/";
        if (!is_dir($temp_path)) {
            createDir($temp_path);
        }
        $temp_file = $temp_path . $client_id . '_' . $micro_time;

        $fopen_mode = $index == 0 ? 'w' : 'a';
        unset($client_id, $temp_path);

        if (@$fp = fopen($temp_file, $fopen_mode)) {
            fwrite($fp, $data);
            fclose($fp);
            unset($fp, $data);
        }
        return true;
    }

    /**
     * 读取长消息的临时文件
     * @param        $client_id
     * @param        $micro_time
     * @param string $type
     * @return bool
     */
    public static function readLongMessageTemp($client_id, $micro_time, $type = 'receive')
    {
        $temp_path = API_ROOT . "/runtime/temp/ws/{$type}/";
        if (!is_dir($temp_path)) {
            createDir($temp_path);
        }
        $temp_file = $temp_path . $client_id . '_' . $micro_time;
        unset($client_id, $micro_time, $temp_path);

        if (@$fp = fopen($temp_file, 'r')) {
            $message = '';
            ini_set('memory_limit', '-1');
            while (false != ($a = fread($fp, self::$maxBinaryBufferSize))) {//返回false表示已经读取到文件末尾
                $message .= $a;
            }
            fclose($fp);
            unset($fp, $a);
            return $message;
        }
        return false;
    }

    /**
     * 读取长消息的临时文件
     * @param        $client_id
     * @param        $micro_time
     * @param string $type
     * @return bool
     */
    public static function readLongMessageTempWithIndex($client_id, $micro_time, $offset = 0, $type = 'receive')
    {
        $temp_path = API_ROOT . "/runtime/temp/ws/{$type}/";
        if (!is_dir($temp_path)) {
            createDir($temp_path);
        }
        $temp_file = $temp_path . $client_id . '_' . $micro_time;
        unset($client_id, $micro_time, $temp_path);

        if (@$fp = fopen($temp_file, 'r')) {
            ini_set('memory_limit', '-1');
            fseek($fp, $offset);
            $message = fread($fp, self::$maxBinaryBufferSize);
            fclose($fp);
            unset($fp, $a);
            return $message;
        }
        return false;
    }

    /**
     * 删除长消息的临时文件
     * @param        $client_id
     * @param        $micro_time
     * @param string $type
     */
    public static function delLongMessageTemp($client_id, $micro_time, $type = 'receive')
    {
        $temp_path = API_ROOT . "/runtime/temp/ws/{$type}/";
        $temp_file = $temp_path . $client_id . '_' . $micro_time;
        unset($client_id, $micro_time, $temp_path);
        if (file_exists($temp_file)) {
            @unlink($temp_file);
        }
    }

    /**
     * 处理长消息
     * @param $client_id
     * @param $data
     * @return mixed
     */
    public static function handleLongMessage($client_id, $data)
    {
        $message = self::readLongMessageTemp($client_id, $data['time']);
        $message = compress_string_decode($message);
        $long_data = self::decodeMessage($message);
        $response = self::onApiMessage($client_id, $long_data);
        self::delLongMessageTemp($client_id, $data['time']);
        unset($client_id, $data, $message, $long_data);
        return $response;
    }

    /**
     * 解码收到的消息
     * @param $message
     * @return mixed
     */
    public static function decodeMessage($message)
    {
        // 解压GZIP
        // $message = zlib_decode($message) ?: $message;
        $message = compress_binary_decode($message) ?: $message;
        // var_dump($message);
        // 解析接收到的消息
        return json_decode($message, true);
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
        // 延长该客户端id对应信息的过期时间
        DI()->cache->expire('ws_server:' . $client_id, 86400);
        // 延长该客户端session id的过期时间
        DI()->cache->expire('session_id:' . $client_id, 86400);

        // DI()->logger->debug("收到消息|client_id|{$client_id}|message|{$message}");
        $data = self::decodeMessage($message);
        // DI()->logger->debug("收到消息|client_id|{$client_id}|message", $data);
        // 请求类型 没有请求类型时返回心跳
        $dataType = $data['type'] ?? 'ping';
        // 默认响应数据
        $response = ['type' => $dataType];
        switch ($dataType) {
            // 客户端 发送心跳请求
            case 'ping':
                $response['type'] = 'pong';
                break;
            // 客户端 响应心跳请求
            case 'pong':
                return;
                break;
            case 'send':
                // var_dump($data);
                self::writeLongMessageTemp($client_id, $data['time'], $data['index'], $data['request']);
                if ($data['sendType'] != 'end') {
                    $response['index'] = intval($data['index']) + 1;
                    $response['time'] = $data['time'];
                } else {
                    $response = self::handleLongMessage($client_id, $data);
                }
                // var_dump($data);
                break;
            case 'receive':
                self::sendLongMessage($client_id, $data['time'], $data['index']);
                return;
                break;
            // 请求接口
            case 'api':
                $response = self::onApiMessage($client_id, $data);
                break;
            default:
                // return;
                break;
        }
        // DI()->logger->info("响应消息|client_id|{$client_id}|response", $response);
        // 下发响应数据到客户端
        self::sendToClient($client_id, $response);
        unset($client_id, $message, $data, $dataType, $response);
    }

    /**
     * 有接口消息时
     * @param $client_id
     * @param $data
     */
    public static function onApiMessage($client_id, $data)
    {
        $response['type'] = 'api';
        // 该客户端id对应的session id
        $session_id = self::sessionSaveHandler()->getSessionId($client_id);
        // var_dump('$client_id---' . $client_id);
        // var_dump('$session_id---' . $session_id);
        // 获取该session id储存的数据
        $_SESSION = self::getSession($session_id);
        // 该客户端id对应的信息
        $server = DI()->cache->get('ws_server:' . $client_id) ?? [];
        $_SERVER = array_merge(($_SERVER ?? []), $server);
        // var_dump("session_id|{$session_id}|session", $_SESSION);
        try {
            $response = self::apiHandler($data, $response);
        } catch (PDOException $exception) {
            DI()->logger->error('api抛出PDO异常', $exception);
            $response['response'] = [
                'ret' => 500,
                'data' => '',
                'msg' => '服务器异常',
            ];
        } catch (Exception $exception) {
            DI()->logger->error('api抛出异常', $exception);
            $response['response'] = [
                'ret' => 500,
                'data' => '',
                'msg' => '服务器异常',
            ];
        }
        // 重新保存session数据
        self::saveSession($session_id, $_SESSION ?? []);
        $_SESSION = [];
        // var_dump("session_id|{$session_id}|session", $_SESSION, '----------');
        unset($session_id, $data, $server, $_SERVER);
        // var_dump($response);
        return $response;
    }

    /**
     * 处理接口请求
     * @param $client_id
     * @param $data
     * @param $response
     * @return mixed
     * @throws Exception
     */
    public static function apiHandler($data, $response)
    {
        foreach (explode('|', urldecode($data['Auth'])) as $item) {
            $key = substr($item, 0, strlen(USER_TOKEN));
            $value = substr($item, strlen(USER_TOKEN));
            if ($key == ADMIN_TOKEN) {
                Admin::$admin_token = $value;
            } else if ($key == USER_TOKEN) {
                User::$user_token = $value;
            }
        }

        // 清空上次请求结果数据
        DI()->response->setRet(200)->setMsg('')->setData([]);
        // 响应操作类
        $pai = new PhalApi();
        // 请求参数
        $request = $data['request'] ?? [];
        // 重新建立api请求
        DI()->request = new Request($request);
        // 获取api返回结果
        $response['response'] = $pai->response()->getResult();
        // 生成 与前端对应的 请求id
        $response['requestId'] = md5($request['s'] . $request['t']);
        return $response;
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
    // Gateway::sendToGroup($room_id, json_encode($new_message));
    // Gateway::joinGroup($client_id, $room_id);
    // Gateway::sendToCurrentClient(json_encode($new_message));
    // Gateway::sendToClient($message_data['to_client_id'], json_encode($new_message));

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
     * 当客户端断开连接时
     * @param string $client_id 客户端id
     */
    public static function onClose($client_id)
    {
        // var_dump('onClose', "client:{$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']} gateway:{$_SERVER['GATEWAY_ADDR']}:{$_SERVER['GATEWAY_PORT']}  client_id:$client_id onClose:''");
        DI()->logger->debug('onClose', "client:{$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']} gateway:{$_SERVER['GATEWAY_ADDR']}:{$_SERVER['GATEWAY_PORT']}  client_id:$client_id onClose:''");
        DI()->cache->delete('ws_server:' . $client_id);
        self::sessionSaveHandler()->delSessionId($client_id);

        // 从房间的客户端列表中删除
        // if (isset($_SESSION['room_id'])) {
        //     $room_id = $_SESSION['room_id'];
        //     $new_message = ['type' => 'logout', 'from_client_id' => $client_id, 'from_client_name' => $_SESSION['client_name'], 'time' => date('Y-m-d H:i:s')];
        //     Gateway::sendToGroup($room_id, json_encode($new_message));
        // }
    }

}
