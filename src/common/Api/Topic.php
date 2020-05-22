<?php

namespace Common\Api;

use Library\Exception\BadRequestException;
use Library\Exception\InternalServerErrorException;

/**
 * 文章接口服务类
 * Topic
 * @author LYi-Ho 2018-11-24 16:06:44
 */
class Topic extends Base
{
    public function getRules()
    {
        $rules = parent::getRules();
        $rules['listData'] += [
            'class_id' => ['name' => 'class_id', 'type' => 'int', 'default' => 0, 'min' => 0, 'require' => true, 'desc' => 'ID'],
        ];
        $rules['create'] = [
            'title' => ['name' => 'title', 'type' => 'string', 'require' => true, 'desc' => '文章标题'],
            'content' => ['name' => 'content', 'type' => 'string', 'require' => true, 'desc' => '正文内容'],
            'subject_id' => ['name' => 'subject_id', 'type' => 'int', 'require' => true, 'desc' => '课程'],
            // 'sticky' => ['name' => 'sticky', 'type' => 'string', 'default' => 'off', 'desc' => '顶置'],
        ];
        return $rules;
    }

    /**
     * 文章 领域层
     * @return \Common\Domain\Topic
     * @throws BadRequestException
     */
    protected function Domain_Topic()
    {
        return self::getDomain();
    }

    /**
     * 文章分类 领域层
     * @return \Common\Domain\Subject
     * @throws BadRequestException
     */
    protected function Domain_Subject()
    {
        return self::getDomain('Subject');
    }

    /**
     * 文章列表
     * @desc 文章列表
     * @return array
     * @throws BadRequestException
     */
    public function listData()
    {
        $data = get_object_vars($this);
        $where = [];
        if ($data['class_id'] > 0) {
            $where['class_id=?'] = $data['class_id'];
        }
        $list = $this->Domain_Topic()::getList($this->limit, $this->offset, $where, 'id,title,add_time', 'id desc');

        $list['subject_name'] = '';
        if ($data['class_id'] > 0) {
            $class = $this->Domain_Subject()::getInfo($data['class_id']);
            if (!empty($class)) {
                $list['subject_name'] = $class['name'];
                // \PhalApi\DI()->response->setMsg($class['name']);
            }
        }
        return $list;
    }

    /**
     * 文章详情数据
     * @desc      获取文章详情数据
     * @return array    数据数组
     * @throws BadRequestException
     */
    public function InfoData()
    {
        self::getModel()->updateViewCount($this->id);// 浏览数+1
        $data = $this->Domain_Topic()::getInfo($this->id);
        $data['detail'] = htmlspecialchars_decode($data['detail']);
        // $detail = $data['detail'];
        // $detail = preg_replace('/((width)=[\'"]+[0-9]+[\'"]+)|((height)=[\'"]+[0-9]+[\'"]+)/i', '', $detail);
        // $detail = preg_replace('/<a\b[^>]+\bhref="([^"]*)"[^>]*>/i', '<a>', $detail);
        // $this->>Domain_Topic()::doUpdate([
        //     'id' => $data['id'],
        //     'detail' =>$detail
        // ]);
        return $data;
    }

    /**
     * @return array
     * @throws BadRequestException
     * @throws InternalServerErrorException
     */
    public function create()
    {
        $data = get_object_vars($this);
        return $this->Domain_Topic()::create($data);
    }

}
