<?php

namespace Admin\Api;

/**
 * 管理员模块接口服务类
 * Admin
 * @author LYi-Ho 2018-11-24 16:06:44
 */
class Admin extends \Common\Api\Admin
{
    use \Common\Api\Common;

    public function getRules()
    {
        $rules = parent::getRules();
        $rules['doInfo'] = [
            'id' => ['name' => 'id', 'type' => 'int', 'require' => false, 'min' => 1, 'desc' => '管理员ID'],
            'user_name' => ['name' => 'user_name', 'type' => 'string', 'require' => true, 'min' => 1, 'desc' => '账号'],
            'password' => ['name' => 'password', 'type' => 'string', 'require' => false, 'desc' => '密码'],
            'status' => ['name' => 'status', 'type' => 'enum', 'range' => ['0', '1'], 'require' => true, 'desc' => '状态'],
        ];
        return $rules;
    }


    /**
     * 修改管理员信息
     * @desc 修改管理员信息
     * @throws \Exception\BadRequestException
     */
    public function doInfo()
    {
        $update = [
            'id' => $this->id,
            'user_name' => $this->user_name,
            'status' => $this->status,
        ];
        if (!empty($this->password)) {
            $update['a_pwd'] = \Common\encrypt($this->password);
            $update['password'] = \Common\pwd_hash($this->password);
        }
        if (empty($this->id)) {
            $update['add_time'] = NOW_TIME;
        }
        $update['edit_time'] = NOW_TIME;
        self::getDomain()::doUpdate($update);
    }


}
