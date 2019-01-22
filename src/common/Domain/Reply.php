<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2018-11-26
 * Time: 11:14:57
 */

namespace Common\Domain;

/**
 * 文章回复 领域层
 * Class Reply
 * @package Common\Domain
 * @author  LYi-Ho 2018-11-26 11:14:57
 */
class Reply
{
    use Common;

    public static function doInfo($data)
    {
        $user = $user = self::getDomain('User')::getCurrentUser(TRUE);// 当前登录的会员

        $topicId = $data['topicId'];

        $reply_data = [];
        $reply_data['topic_id'] = $topicId;
        $reply_data['user_id'] = $user['id'];
        $reply_data['name'] = $user['user_name'];
        $reply_data['email'] = $user['email'];
        $reply_data['detail'] = $data['content'];
        if (!$data['id']) {
            $reply_sort = self::getMax(['sort' => $topicId], 'sort') + 1;
            $reply_data['sort'] = $reply_sort;
            $reply_data['add_time'] = NOW_TIME;
            $reply_data['edit_time'] = NOW_TIME;
            self::getModel('Topic')->updateReplyCount($topicId);// 更新回复数量
        } else {
            $reply_data['edit_time'] = NOW_TIME;
        }
        self::doUpdate($reply_data);
    }

}
