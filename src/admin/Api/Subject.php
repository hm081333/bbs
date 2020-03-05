<?php

namespace Admin\Api;

/**
 * 科目接口服务类
 * Subject
 * @author LYi-Ho 2018-11-24 16:06:44
 */
class Subject extends \Common\Api\Subject
{
    use \Common\Api\Common;

    public function getRules()
    {
        $rules = parent::getRules();
        $rules['doInfo'] = [
            'id' => ['name' => 'id', 'type' => 'int', 'require' => false, 'default' => 0, 'desc' => '课程ID'],
            'name' => ['name' => 'name', 'type' => 'string', 'require' => true, 'min' => 1, 'desc' => '课程名称'],
            'tips' => ['name' => 'tips', 'type' => 'string', 'require' => false, 'desc' => '课程说明'],
            'status' => ['name' => 'status', 'type' => 'enum', 'range' => ['0', '1'], 'require' => true, 'desc' => '状态'],
        ];
        return $rules;
    }

    /**
     * 修改课程信息
     * @desc 修改课程信息
     * @throws \Library\Exception\BadRequestException
     */
    public function doInfo()
    {
        $update = [
            'id' => $this->id,
            'name' => $this->name,
            'tips' => $this->tips,
            'status' => $this->status,
        ];
        if (empty($this->id)) {
            $update['add_time'] = time();
        }
        $update['edit_time'] = time();
        self::getDomain()::doUpdate($update);
    }


}
