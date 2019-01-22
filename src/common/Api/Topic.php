<?php

namespace Common\Api;


/**
 * 文章接口服务类
 * Topic
 * @author LYi-Ho 2018-11-24 16:06:44
 */
class Topic extends Base
{
    use Common;

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
     * 文章列表
     * @desc 文章列表
     * @return array
     */
    public function listData()
    {
        $data = get_object_vars($this);
        $where = [];
        if ($data['class_id'] > 0) {
            $where['class_id=?'] = $data['class_id'];
        }
        $list = self::getDomain()::getList($this->limit, $this->offset, $where, '*', 'id desc');

        $list['subject_name'] = '';
        if ($data['class_id'] > 0) {
            $class = self::getDomain('Subject')::getInfo($data['class_id']);
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
     */
    public function InfoData()
    {
        self::getModel()->updateViewCount($this->id);// 浏览数+1
        $data = self::getDomain()::getInfo($this->id);
        // $detail = $data['detail'];
        // $detail = preg_replace('/((width)=[\'"]+[0-9]+[\'"]+)|((height)=[\'"]+[0-9]+[\'"]+)/i', '', $detail);
        // $detail = preg_replace('/<a\b[^>]+\bhref="([^"]*)"[^>]*>/i', '<a>', $detail);
        // self::getDomain()::doUpdate([
        //     'id' => $data['id'],
        //     'detail' =>$detail
        // ]);
        return $data;
    }

    public function create()
    {
        $data = get_object_vars($this);
        return self::getDomain()::create($data);
    }

}
