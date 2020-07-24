<?php

namespace Common\Cache;

use Library\Abstracts\Cache;
use Library\Exception\BadRequestException;

/**
 * 聊天 缓存层
 * Class Chat
 * @package Common\Cache
 * @author  LYi-Ho 2020-07-24 18:18:44
 */
class Chat extends Cache
{
    protected function getTableName()
    {
        return 'chat';
    }

    /**
     * 聊天 领域层
     * @return \Common\Domain\Chat
     * @throws BadRequestException
     */
    protected function Domain_Chat()
    {
        return self::getDomain();
    }

    /**
     * 聊天 模型层
     * @return \Common\Model\Chat
     * @throws BadRequestException
     */
    protected function Model_Chat()
    {
        return self::getModel();
    }

    /**
     * 获取会员缓存
     * @param bool|int $chat_id
     * @return array|mixed|null
     * @throws BadRequestException
     */
    public function get($chat_id = false)
    {
        if (!empty($chat_id)) {
            $name = $this->getTableName();
            $chat = parent::get($name);
            if ($chat == null) {
                $chat = $this->Domain_Chat()::getInfo($chat_id);
                if ($chat) {
                    $chat['user_ids_str'] = $chat['user_ids'];
                    $chat['user_ids'] = explode(',', $chat['user_ids_str']);
                    $chat['is_delete'] = boolval($chat['is_delete']);
                    $chat['is_group'] = boolval($chat['is_group']);
                    //  群聊人数
                    $chat['people_count'] = count($chat['user_ids']);
                    parent::set($chat_id, $chat);
                }
            }
        }
        return $chat ?? [];
    }

    /**
     * 更新会员
     * @param $where
     * @param $data
     */
    public function update($where, $data)
    {
        if (is_array($where)) {
            $limit = 100;
            $offset = 0;
            while ($chats = $this->Model_Chat()->getListLimitByWhere($limit, $offset, $where, 'id desc', 'id')) {
                foreach ($chats as $chat) {
                    $this->Model_Chat()->update($chat['id'], $data);
                    // 删除缓存
                    $this->delete($chat['id']);
                }
                $offset += $limit;
            }
            // $this->Model_Chat()->updateByWhere($where, $data);
        } else {
            $this->Model_Chat()->update($where, $data);
            // 删除缓存
            $this->delete($where);
        }
    }

}
