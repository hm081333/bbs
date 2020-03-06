<?php

namespace Common\Api;

use Library\Traits\Api;

/**
 * 系统操作 接口服务类
 * @ignore
 * @author LYi-Ho 2018-11-24 16:57:36
 */
class System extends Base
{
    use Api;

    /**
     * 接口参数规则
     * @return array
     */
    public function getRules()
    {
        $rules = parent::getRules();
        $rules['reset'] = [
            'password' => ['name' => 'password', 'type' => 'string', 'require' => true, 'desc' => '密码'],
        ];
        $rules['backup'] = [
            'password' => ['name' => 'password', 'type' => 'string', 'require' => true, 'desc' => '密码'],
        ];
        $rules['restore'] = [
            'name' => ['name' => 'name', 'type' => 'string', 'require' => true, 'desc' => 'SQL文件名'],
            'password' => ['name' => 'password', 'type' => 'string', 'require' => true, 'desc' => '密码'],
        ];
        $rules['reset'] = [
            'password' => ['name' => 'password', 'type' => 'string', 'require' => true, 'desc' => '密码'],
        ];
        return $rules;
    }

    /**
     * 备份数据
     * @desc 备份数据
     */
    public function backup()
    {
        $data = get_object_vars($this);
        return self::getDomain()::backup($data);
    }

    /**
     * 还原数据
     * @desc 还原数据
     */
    public function restore()
    {
        $data = get_object_vars($this);
        return self::getDomain()::restore($data);
    }

    /**
     * 重置系统
     * @desc 重置系统
     */
    public function reset()
    {
        $data = get_object_vars($this);
        return self::getDomain()::reset($data);
    }

    /**
     * 备份文件列表
     * @desc 备份文件列表
     */
    public function backupList()
    {
        return self::getDomain()::dirFile(API_ROOT . '/data/');
    }


}
