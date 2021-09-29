<?php
declare (strict_types=1);

namespace app\chat\controller;

use app\BaseController;
use library\exception\BadRequestException;
use think\Request;
use think\swoole\Websocket;

class ChatMessage extends BaseController
{
    /**
     * 获取指定聊天室的聊天消息记录
     */
    public function chatMessageListData()
    {
        // 开始位置
        $offset = $this->request->param('offset/d', 0);
        // 数量
        $limit = $this->request->param('limit/d', 10);
        // 查询字段
        $field = $this->request->param('field', 'id,user_id,message,add_time');
        // 查询条件
        $where = $this->request->param('where', []);
        $order = $this->request->param('order', 'id desc');

        $user = $this->request->getCurrentUser(true);
        $list = [
            'total' => $this->modelChatMessage->where($where)->count(),
            'rows' => new \think\model\Collection(),
            'offset' => $offset,
            'limit' => $limit,
        ];
        if ($list['total'] <= 0) {
            throw new BadRequestException(T('没有更多消息'));
        }
        $list['rows'] = $this->modelChatMessage->where($where)->field($field)->order($order)->limit($offset, $limit)->select();

        $list['rows'] = $list['rows']->map(function (\app\model\ChatMessage $row) use ($user) {
            $message_user = $this->modelUser->where(['id' => $row['user_id']])->find();
            return [
                'message_id' => $row['id'],
                'message' => strToHtml($row['message']),
                'type' => $row['user_id'] == $user['id'] ? 'send' : 'receive',
                'user' => [
                    'user_id' => $message_user['id'],
                    'user_name' => $message_user['user_name'],
                    'nick_name' => $message_user['nick_name'],
                    'logo' => $message_user['logo'],
                ],
                'add_time' => $row['add_time'],
                // 'add_time_date' => $row['add_time_date'],
                // 'add_time_unix' => $row['add_time_unix'],
            ];
        });
        return success('', $list);
    }

    /**
     * 发送消息
     * @param int    $chat_id   聊天室ID
     * @param string $message   消息内容
     * @param int    $send_time 发送时间
     * @return mixed
     */
    public function sendChatMessage(int $chat_id, string $message, int $send_time)
    {
        $user = $this->request->getCurrentUser(true);
        // var_dump($user);
        // var_dump($chat_id);
        // var_dump($message);
        // var_dump($send_time);
        $chat_message = $this->modelChatMessage;
        $chat_message->appendData([
            'chat_id' => $chat_id,
            'user_id' => $user['id'],
            'message' => $message,
            'add_time' => $send_time,
        ]);
        $chat_message->save();
        // var_dump($result);
        $message_info = [
            'message_id' => $chat_message->id,
            'message' => strToHtml($message),
            'type' => 'send',
            'user' => [
                'user_id' => $user['id'],
                'user_name' => $user['user_name'],
                'nick_name' => $user['nick_name'],
                'logo' => $user['logo'],
            ],
            'add_time' => unix_formatter($send_time, true),
            'add_time_date' => unix_formatter($send_time),
            'add_time_unix' => $send_time,
        ];
        // var_dump($message_info);
        $this->sendChatMessageToClient($chat_id, $message_info);
        $this->modelChat->where([
            ['id', '=', $chat_id],
        ])->update([
            'last_time' => $send_time,

        ]);
        return $message_info;
    }

    protected function sendChatMessageToClient($chat_id, $message_info)
    {
        $message_info['chat_id'] = $chat_id;
        $message_info['type'] = 'receive';
        $message_info['last_time_short'] = sortTime($message_info['add_time_unix']);

        $chat_info = $this->modelChat->field(['user_ids'])->where([
            ['id', '=', $chat_id],
        ])->find();
        foreach ($chat_info['user_ids'] as $chat_user_id) {
            // 发送者不需要推送
            if ($chat_user_id == $message_info['user']['user_id']) continue;
            $this->request->websocket->to("user.{$chat_user_id}")->emit('push_message', $message_info);
            // $chat_user_info = $this->modelUser->find($chat_user_id);
            // if (!empty($chat_user_info)) {
            //     Push::pushMessage($chat_user_info['client_id'], $message_info);
            // }
        }
        // var_dump($chat_user_ids);
        return true;
    }
}
