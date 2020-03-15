<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2018-11-26
 * Time: 11:14:57
 */

namespace Common\Domain;

use Library\Exception\BadRequestException;
use Library\Exception\InternalServerErrorException;
use Library\Traits\Domain;
use function PhalApi\T;

/**
 * 文章 领域层
 * Class Subject
 * @package Common\Domain
 * @author  LYi-Ho 2018-11-26 11:14:57
 */
class Topic
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
     * 新建文章
     * @param $data
     * @return array
     * @throws BadRequestException
     * @throws InternalServerErrorException
     */
    public static function create($data)
    {
        // if (empty($data['title'])) {
        //     throw new PhalApi_Exception_Error(T('请输入文章标题'), 1);// 抛出普通错误 T标签翻译
        // } else if (empty($data['content'])) {
        //     throw new PhalApi_Exception_Error(T('请输入正文内容'), 1);// 抛出普通错误 T标签翻译
        // } else if (empty($data['subject_id'])) {
        //     throw new PhalApi_Exception_Error(T('请选择课程'), 1);// 抛出普通错误 T标签翻译
        // }
        $user = self::Domain_User()::getCurrentUser(true);// 当前登录的会员
        $topic_model = self::getModel();
        $insert_data = [];
        $insert_data['class_id'] = $data['subject_id'];
        $insert_data['title'] = $data['title'];
        $insert_data['detail'] = htmlspecialchars($data['content']);
        $insert_data['user_id'] = $user['id'];
        $insert_data['name'] = $user['user_name'];
        $insert_data['email'] = $user['email'];
        $insert_data['add_time'] = time();
        $insert_data['edit_time'] = time();
        /*if ($this->sticky == 'on') {
            $insert_data['sticky'] = 1;
        }*/
        $topic_id = $topic_model->insert($insert_data);
        if ($topic_id) {
            self::DI()->response->setMsg(T('发布成功'));
            return ['topic_id' => $topic_id];
        } else {
            throw new InternalServerErrorException(T('发布失败'), 2);// 抛出服务端错误
        }
    }
}
