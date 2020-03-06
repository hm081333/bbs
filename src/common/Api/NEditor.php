<?php


namespace Common\Api;

use Library\Traits\Api;

/**
 * 上传接口服务类
 * @ignore
 * @author LYi-Ho 2018-11-24 16:57:36
 */
class NEditor extends Base
{
    use Api;

    /**
     * 接口参数规则
     * @return array
     */
    public function getRules()
    {
        $rules = parent::getRules();
        $rules['config'] = [];
        $rules['upload'] = [
            'action' => ['name' => 'action', 'type' => 'enum', 'range' => ['image', 'scrawl', 'video', 'file'], 'require' => true, 'desc' => '上传类型'],
        ];
        $rules['catch'] = [];
        return $rules;
    }

    public function config()
    {
        return \Common\Common\NEditor::getConfig();
    }

    public function upload()
    {
        $result = \Common\Common\NEditor::upload($this->action);
        return $result['url'];
    }

    public function catch()
    {
        $result = \Common\Common\NEditor::crawler();
        exit(json_encode($result));
    }


}
