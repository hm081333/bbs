<?php

namespace Common\Api;

/**
 * 文章回复 接口服务类
 * Reply
 * @author LYi-Ho 2018-11-24 16:06:44
 */
class Reply extends Base
{
    use Common;

    public function getRules()
    {
        $rules = parent::getRules();
        $rules['doInfo'] = [
            'id' => ['name' => 'replyId', 'type' => 'int', 'require' => TRUE, 'min' => 0, 'default' => '0', 'desc' => "回复ID"],
            'topicId' => ['name' => 'topicId', 'type' => 'int', 'require' => TRUE, 'min' => 1, 'desc' => "文章ID"],
            'content' => ['name' => 'content', 'type' => 'string', 'require' => TRUE, 'desc' => "回复内容"],
        ];
        return $rules;
    }

    public function doInfo()
    {
        $data = get_object_vars($this);
        self::getDomain()::doInfo($data);
    }

}
