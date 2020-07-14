<?php

namespace Chat\Api;

use Library\Exception\BadRequestException;

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
        $rules['chatMessageListData'] = [
            'chat_id' => ['name' => 'chat_id', 'type' => 'int', 'require' => true, 'default' => 0, 'desc' => '聊天室ID'],
            'offset' => ['name' => 'offset', 'type' => 'int', 'default' => 0, 'desc' => "开始位置"],
            'limit' => ['name' => 'limit', 'type' => 'int', 'default' => PAGE_NUM, 'desc' => '数量'],
            'field' => ['name' => 'field', 'type' => 'string', 'default' => '*', 'desc' => '查询字段'],
            'where' => ['name' => 'where', 'type' => 'array', 'default' => [], 'desc' => '查询条件'],
            'order' => ['name' => 'order', 'type' => 'string', 'default' => 'id desc', 'desc' => '排序方式'],
        ];
        $rules['listData'] = [
            'offset' => ['name' => 'offset', 'type' => 'int', 'default' => 0, 'desc' => "开始位置"],
            'limit' => ['name' => 'limit', 'type' => 'int', 'default' => PAGE_NUM, 'desc' => '数量'],
            'field' => ['name' => 'field', 'type' => 'string', 'default' => '*', 'desc' => '查询字段'],
            'where' => ['name' => 'where', 'type' => 'array', 'default' => [], 'desc' => '查询条件'],
            'order' => ['name' => 'order', 'type' => 'string', 'default' => 'id desc', 'desc' => '排序方式'],
        ];
        return $rules;
    }

    /**
     * 聊天 领域层
     * @return \Common\Domain\ChatMessage
     * @throws BadRequestException
     */
    protected function DomainChatMessage()
    {
        return self::getDomain('ChatMessage');
    }

    /**
     * 好友 领域层
     * @return \Common\Domain\Friend
     * @throws BadRequestException
     */
    protected function DomainFriend()
    {
        return self::getDomain('Friend');
    }

    /**
     * 获取指定聊天室的聊天消息记录
     */
    public function chatMessageListData()
    {
        $user = $this->Domain_User()::getCurrentUser(true);
        $this->where['chat_id'] = $this->chat_id;
        // var_dump($user);
        $list = $this->DomainChatMessage()::getList($this->limit, $this->offset, $this->where, $this->field, $this->order);
        var_dump($list);
        die;
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

}
