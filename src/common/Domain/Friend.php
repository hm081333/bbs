<?php

namespace Common\Domain;

use Library\Traits\Domain;

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
}
