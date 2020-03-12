<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2018-11-26
 * Time: 11:14:57
 */

namespace Common\Domain;

use Library\Exception\BadRequestException;
use Library\Traits\Domain;

/**
 * 文章回复 领域层
 * Class Reply
 * @package Common\Domain
 * @author  LYi-Ho 2018-11-26 11:14:57
 */
class Reply
{
    use Domain;

    /**
     * 会员 领域层
     * @return User
     * @throws BadRequestException
     */
    protected static function Domain_User()
    {
        return self::getDomain('User');
    }

    /**
     * 文章 数据层
     * @return \Common\Model\Topic
     * @throws BadRequestException
     */
    protected static function Model_Topic()
    {
        return self::getModel('Topic');
    }

    /**
     * 回复 数据层
     * @return \Common\Model\Reply
     * @throws BadRequestException
     */
    protected static function Model_Reply()
    {
        return self::getModel('Reply');
    }

    /**
     * 发表回复
     * @param $data
     * @throws BadRequestException
     */
    public static function doInfo($data)
    {
        $user = $user = self::Domain_User()::getCurrentUser(true);// 当前登录的会员

        $topicId = $data['topicId'];

        $reply_data = [];
        $reply_data['topic_id'] = $topicId;
        $reply_data['user_id'] = $user['id'];
        $reply_data['name'] = $user['user_name'];
        $reply_data['email'] = $user['email'];
        // HTML文本转为实体
        $reply_data['detail'] = htmlspecialchars($data['content']);
        if (!$data['id']) {
            $reply_sort = self::getMax(['sort' => $topicId], 'sort') + 1;
            $reply_data['sort'] = $reply_sort;
            $reply_data['add_time'] = time();
            $reply_data['edit_time'] = time();
            self::Model_Topic()->updateReplyCount($topicId);// 更新回复数量
        } else {
            $reply_data['edit_time'] = time();
        }
        self::doUpdate($reply_data);
    }

}
