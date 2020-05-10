<?php

namespace Admin\Api;

/**
 * 文章接口服务类
 * Topic
 * @author LYi-Ho 2018-11-24 16:06:44
 */
class Topic extends \Common\Api\Topic
{
    public function getRules()
    {
        $rules = parent::getRules();
        return $rules;
    }

    /**
     * 文章列表
     * @desc 文章列表
     * @return array
     */
    public function listData()
    {
        $data = get_object_vars($this);
        $where = [];
        if ($data['class_id'] > 0) {
            $where['ly_topic.class_id=?'] = $data['class_id'];
        }
        $list = self::getDomain()::getList($this->limit, $this->offset, $where, 'ly_topic.id,ly_topic.add_time,ly_topic.edit_time,ly_topic.reply,ly_topic.sticky,ly_topic.title,ly_topic.view,user.user_name,class.name', 'ly_topic.id desc');
        return $list;
    }


}
