<?php

namespace Common\Domain;

use Library\Exception\BadRequestException;
use Library\Traits\Domain;
use function Common\res_path;
use function PhalApi\T;

/**
 * 好友 领域层
 * Class Friend
 * @package Common\Domain
 * @author  LYi-Ho 2020-05-08 12:07:13
 */
class Friend
{
    use Domain;

    /**
     * 聊天室 领域层
     * @return \Common\Model\Chat
     * @throws BadRequestException
     */
    protected function Model_Chat()
    {
        return self::getModel('Chat');
    }

    /**
     * 用户 领域层
     * @return \Common\Domain\User
     * @throws BadRequestException
     */
    protected function Domain_User()
    {
        return self::getDomain('User');
    }

    /**
     * 用户 缓存层
     * @return \Common\Cache\User
     * @throws BadRequestException
     */
    protected function Cache_User()
    {
        return self::getCache('User');
    }

    /**
     * 好友状态 名称
     * @param bool|int $status
     * @return string|string[]
     */
    public function friendStatusName($status = false)
    {
        $names = [
            0 => '非好友',
            1 => '对方非好友',
            2 => '非对方好友',
            3 => '好友',
            4 => '已拉黑',
            5 => '对方已拉黑',
        ];
        if ($status != false) {
            return $names[$status] ?? '';
        }
        return $names;
    }

    /**
     * 好友状态
     * @param $user_id
     * @param $friend_id
     * @return int
     */
    public function friendStatus($user_id, $friend_id)
    {
        $friend_user = self::getInfoByWhere(['friend_id' => $user_id, 'user_id' => $friend_id], 'id');
        $user_friend = self::getInfoByWhere(['user_id' => $user_id, 'friend_id' => $friend_id], 'id');
        if (!$friend_user && !$user_friend) {
            $status = 0;
        } else if ($friend_user && !$user_friend) {
            $status = 1;
        } else if (!$friend_user && $user_friend) {
            $status = 2;
        } else if ($friend_user && $user_friend) {
            $status = 3;
        }
        return $status;
    }

    public function info($friend_id)
    {
        $user = $this->Domain_User()::getCurrentUser(true);
        $friend = $this->Cache_User()->get($friend_id);
        if (empty($friend)) {
            throw new BadRequestException(T('无法找到该用户'));
        }
        $status = $this->friendStatus($user['id'], $friend['id']);
        $status_name = $this->friendStatusName($status);
        $chat_id = $this->Model_Chat()->queryRows("SELECT `id` FROM `ly_chat` WHERE FIND_IN_SET(?, `user_ids`) AND FIND_IN_SET(?, `user_ids`);", [
            $user['id'],
            $friend['id'],
        ]);
        if (empty($chat_id)) {
            $chat_id = $this->Model_Chat()->insert([
                'user_ids' => $user['id'] . ',' . $friend['id'],
                'add_time' => NOW_TIME,
                'edit_time' => NOW_TIME,
                'last_time' => NOW_TIME,
            ]);
        } else {
            $chat_id = $chat_id[0]['id'];
        }
        // var_dump($chat_id);
        return [
            'status' => $status,
            'statusName' => $status_name,
            'chat_id' => $chat_id,
            'friendInfo' => [
                'id' => $friend['id'],
                'logo' => empty($friend['logo']) ? '' : res_path($friend['logo']),
                'nick_name' => $friend['nick_name'],
                'user_name' => $friend['user_name'],
            ],
        ];
    }
}
