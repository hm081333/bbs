<?php

namespace Common\Api;

/**
 * 管理员模块接口服务类
 * Admin
 * @author LYi-Ho 2018-11-24 16:06:44
 */
class Admin extends Base
{
    use Common;

    public function getRules()
    {
        $rules = parent::getRules();
        $rules['signIn'] = [
            'user_name' => ['name' => 'user_name', 'type' => 'string', 'require' => true, 'min' => 1, 'desc' => '用户名'],
            'password' => ['name' => 'password', 'type' => 'string', 'require' => true, 'min' => 1, 'desc' => '密码'],
            'remember' => ['name' => 'remember', 'type' => 'boolean', 'default' => false, 'desc' => '记住我'],
        ];
        $rules['getCurrentAdmin'] = [];
        $rules['signOut'] = [];
        return $rules;
    }

    /**
     * 登录接口
     * @desc 根据账号和密码进行登录操作
     * @return array
     * @throws \Exception\BadRequestException
     */
    public function signIn()
    {
        $data = get_object_vars($this);
        return self::getDomain()::doSignIn($data);
    }

    /**
     * 获取当前登录的管理员信息
     * @return array
     */
    public function getCurrentAdmin()
    {
        return [
            'admin' => self::getDomain()::getCurrentAdminInfo(),
        ];
    }

    /**
     * 退出登录接口
     * @desc 退出登录接口
     */
    public function signOut()
    {
        self::getDomain()::doSignOut();
    }


}
