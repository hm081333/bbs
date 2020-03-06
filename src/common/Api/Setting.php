<?php

namespace Common\Api;

use Library\Traits\Api;

/**
 * 管理员模块接口服务类
 * Setting
 * @author LYi-Ho 2018-11-24 16:06:44
 */
class Setting extends Base
{
    use Api;

    public function getRules()
    {
        $rules = parent::getRules();
        $rules['get'] = [
            'setting_key_name' => ['name' => 'setting_key_name', 'type' => 'string', 'require' => true, 'desc' => '配置参数键值'],
        ];
        $rules['set'] = [
            'setting_key_name' => ['name' => 'setting_key_name', 'type' => 'string', 'require' => true, 'desc' => '配置参数键值'],
            'setting_key_data' => ['name' => 'setting_key_data', 'type' => 'array', 'require' => true, 'desc' => '配置参数值数组'],
        ];
        return $rules;
    }

    /**
     * 获取配置信息接口
     * @desc 获取配置信息
     * @return array
     */
    public function get()
    {
        // $data = get_object_vars($this);
        // return self::getDomain()::getSetting($data['name']);
        return self::getDomain()::getSetting($this->setting_key_name);
    }

    /**
     * 设置配置信息接口
     * @desc 设置配置信息
     * @return array
     */
    public function set()
    {
        return self::getDomain()::updateSetting($this->setting_key_name, $this->setting_key_data);
    }


}
