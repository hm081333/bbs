<?php

namespace Chat\Api;

use Library\Exception\BadRequestException;
use Library\Exception\Exception;
use function Common\DI;
use function Common\res_path;
use function Common\unix_formatter;
use function PhalApi\T;

/**
 * 聊天消息 接口服务
 * ChatMessage
 * @author LYi-Ho 2020-05-10 21:08:17
 */
class ChatMessage extends \Common\Api\Chat
{
    public function getRules()
    {
        $rules = parent::getRules();
        $rules['listData'] = [
            'offset' => ['name' => 'offset', 'type' => 'int', 'default' => 0, 'desc' => "开始位置"],
            'limit' => ['name' => 'limit', 'type' => 'int', 'default' => PAGE_NUM, 'desc' => '数量'],
            'field' => ['name' => 'field', 'type' => 'string', 'default' => '*', 'desc' => '查询字段'],
            'where' => ['name' => 'where', 'type' => 'array', 'default' => [], 'desc' => '查询条件'],
            'order' => ['name' => 'order', 'type' => 'string', 'default' => 'id DESC', 'desc' => '排序方式'],
        ];
        $rules['chatMessageListData'] = $rules['listData'];
        $rules['sendChatMessage'] = [
            'chat_id' => ['name' => 'chat_id', 'type' => 'int', 'require' => true, 'desc' => '聊天室ID'],
            'message' => ['name' => 'message', 'type' => 'string', 'require' => true, 'desc' => '消息内容'],
            'send_time' => ['name' => 'send_time', 'type' => 'int', 'require' => true, 'desc' => "发送时间"],
        ];
        return $rules;
    }

    /**
     * 聊天 领域层
     * @return \Common\Domain\Chat
     * @throws BadRequestException
     */
    protected function Domain_Chat()
    {
        return self::getDomain('Chat');
    }

    /**
     * 聊天 领域层
     * @return \Common\Domain\ChatMessage
     * @throws BadRequestException
     */
    protected function Domain_ChatMessage()
    {
        return self::getDomain('ChatMessage');
    }

    /**
     * 好友 领域层
     * @return \Common\Domain\Friend
     * @throws BadRequestException
     */
    protected function Domain_Friend()
    {
        return self::getDomain('Friend');
    }

    /**
     * 获取指定聊天室的聊天消息记录
     */
    public function chatMessageListData()
    {
        $user = $this->Domain_User()::getCurrentUser(true);
        $this->field = 'id,user_id,message,add_time';
        // $this->where['id < ?']='';
        $list = $this->Domain_ChatMessage()::getList($this->limit, $this->offset, $this->where, $this->field, $this->order);
        if ($list['total'] <= 0) {
            throw new Exception(T('没有更多消息'));
        }
        $rows = $list['rows'];
        $list['rows'] = [];
        foreach ($rows as $row) {
            $message_user = $this->Domain_User()->get($row['user_id']);
            $list['rows'][] = [
                'message_id' => $row['id'],
                'message' => \Common\strToHtml($row['message']),
                'type' => $row['user_id'] == $user['id'] ? 'send' : 'receive',
                'user' => [
                    'user_id' => $message_user['id'],
                    'user_name' => $message_user['user_name'],
                    'nick_name' => $message_user['nick_name'],
                    'logo' => empty($message_user['logo']) ? '' : res_path($message_user['logo']),
                ],
                'add_time' => $row['add_time'],
                'add_time_date' => $row['add_time_date'],
                'add_time_unix' => $row['add_time_unix'],
            ];
        }
        return $list;
    }

    /**
     * 聊天消息列表
     * @return array|mixed
     * @throws BadRequestException
     */
    public function listData()
    {
        $user = $this->Domain_User()::getCurrentUser(true);
        var_dump($user);
        die;
    }

    public function sendChatMessage()
    {
        $chat_id = $this->chat_id;
        $message = $this->message;
        $send_time = $this->send_time;
        return $this->Domain_ChatMessage()->sendChatMessage($chat_id, $message, $send_time);
    }

}
