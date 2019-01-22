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

class Events
{

    /**
     * 有消息时
     * @param int   $client_id
     * @param mixed $message
     * @throws Exception
     * @return void
     */
    public static function onMessage($client_id, $message)
    {
        $data = json_decode($message, true);
        if (!$data) {
            Gateway::sendToClient($client_id, '{"type":"ping"}');
            exit(0);
        }
        $data['s'] = $data['s'] ?? 'Common.Base.Index';// 访问服务参数
        $service = explode('.', $data['s']);// 拆解访问参数
        // 重构访问服务参数
        if (count($service) == 2) {
            $service = array_merge([$_SESSION['name_space']], $service);// 命名空间名称放在首位
            $data['s'] = implode('.', $service);// 重构访问服务参数
        }
        \PhalApi\DI()->request = new \PhalApi\Request($data);
        $pai = new \PhalApi\PhalApi();
        $return = $pai->response()->getResult();
        Gateway::sendToClient($client_id, json_encode($return, true));
    }
    // Gateway::sendToGroup($room_id, json_encode($new_message));
    // Gateway::joinGroup($client_id, $room_id);
    // Gateway::sendToCurrentClient(json_encode($new_message));
    // Gateway::sendToClient($message_data['to_client_id'], json_encode($new_message));

    /**
     * 当客户端断开连接时
     * @param integer $client_id 客户端id
     */
    public static function onClose($client_id)
    {
        // debug
        echo "client:{$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']} gateway:{$_SERVER['GATEWAY_ADDR']}:{$_SERVER['GATEWAY_PORT']}  client_id:$client_id onClose:''\n";

        // 从房间的客户端列表中删除
        // if (isset($_SESSION['room_id'])) {
        //     $room_id = $_SESSION['room_id'];
        //     $new_message = ['type' => 'logout', 'from_client_id' => $client_id, 'from_client_name' => $_SESSION['client_name'], 'time' => date('Y-m-d H:i:s')];
        //     Gateway::sendToGroup($room_id, json_encode($new_message));
        // }
    }

}
