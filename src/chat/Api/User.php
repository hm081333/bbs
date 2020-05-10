<?php

namespace Chat\Api;

use Library\Exception\BadRequestException;

/**
 * 用户模块接口服务
 * User
 * @author LYi-Ho 2020-05-04 01:08:15
 */
class User extends \Common\Api\User
{
    public function getRules()
    {
        $rules = parent::getRules();
        $rules['editNickName'] = [
            'nick_name' => ['name' => 'nick_name', 'type' => 'string', 'default' => '', 'desc' => '更新的名称'],
        ];
        $rules['editGender'] = [
            'sex' => ['name' => 'sex', 'type' => 'enum', 'range' => ['1', '2'], 'require' => false, 'desc' => '性别'],
        ];
        $rules['editSignature'] = [
            'signature' => ['name' => 'signature', 'type' => 'string', 'default' => '', 'desc' => '个性签名'],
        ];
        $rules['editLogo'] = [
            'logo' => ['name' => 'logo', 'type' => 'string', 'default' => '', 'desc' => '头像文件的相对路径'],
        ];
        return $rules;
    }

    /**
     * 修改昵称
     * @return array
     * @throws BadRequestException
     */
    public function editNickName()
    {
        $this->Domain_User()->editUser(null, ['nick_name' => $this->nick_name]);
        return $this->Domain_User()::getCurrentUserInfo();
    }

    /**
     * 修改性别
     * @return array
     * @throws BadRequestException
     */
    public function editGender()
    {
        $this->Domain_User()->editUser(null, ['sex' => $this->sex]);
        return $this->Domain_User()::getCurrentUserInfo();
    }

    /**
     * 修改个性签名
     * @return array
     * @throws BadRequestException
     */
    public function editSignature()
    {
        $this->Domain_User()->editUser(null, ['signature' => $this->signature]);
        return $this->Domain_User()::getCurrentUserInfo();
    }

    /**
     * 修改个人头像
     * @return array
     * @throws BadRequestException
     */
    public function editLogo()
    {
        $this->Domain_User()->editUser(null, ['logo' => $this->logo]);
        return $this->Domain_User()::getCurrentUserInfo();
    }

}
