<?php

namespace Common\Api;

use Library\Exception\BadRequestException;
use Library\Exception\InternalServerErrorException;
use Library\Traits\Api;
use function Common\encrypt;
use function Common\pwd_hash;
use function PhalApi\T;

/**
 * 用户模块接口服务
 * User
 * @author LYi-Ho 2018-11-24 16:06:44
 */
class User extends Base
{
    use Api;

    public function getRules()
    {
        $rules = parent::getRules();
        $rules['signIn'] = [
            'user_name' => ['name' => 'user_name', 'type' => 'string', 'require' => true, 'min' => 1, 'desc' => '用户名'],
            'password' => ['name' => 'password', 'type' => 'string', 'require' => true, 'min' => 6, 'desc' => '密码'],
            'remember' => ['name' => 'remember', 'type' => 'boolean', 'default' => false, 'desc' => '记住我'],
        ];
        $rules['signUp'] = [
            'user_name' => ['name' => 'user_name', 'type' => 'string', 'require' => true, 'min' => 1, 'desc' => '用户名'],
            'password' => ['name' => 'password', 'type' => 'string', 'require' => true, 'min' => 6, 'desc' => '密码'],
            'email' => ['name' => 'email', 'type' => 'string', 'require' => true, 'regex' => "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i", 'desc' => '邮箱'],
            'real_name' => ['name' => 'real_name', 'type' => 'string', 'require' => true, 'min' => 1, 'desc' => '姓名'],
            'birth_time' => ['name' => 'birth', 'type' => 'int', 'require' => true, 'desc' => '生日'],
            'sex' => ['name' => 'sex', 'type' => 'enum', 'range' => ['1', '2'], 'require' => true, 'desc' => '性别'],
        ];
        $rules['doInfo'] = [
            'id' => ['name' => 'id', 'type' => 'int', 'require' => true, 'min' => 1, 'desc' => '用户ID'],
            'user_name' => ['name' => 'user_name', 'type' => 'string', 'require' => false, 'min' => 1, 'desc' => '用户名'],
            'nick_name' => ['name' => 'nick_name', 'type' => 'string', 'require' => false, 'min' => 1, 'desc' => '名字'],
            'real_name' => ['name' => 'real_name', 'type' => 'string', 'require' => false, 'min' => 1, 'desc' => '姓名'],
            'password' => ['name' => 'password', 'type' => 'string', 'require' => false, 'desc' => '密码'],
            'email' => ['name' => 'email', 'type' => 'string', 'require' => false, 'regex' => "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i", 'desc' => '邮箱'],
            'birth_time' => ['name' => 'birth', 'type' => 'date', 'format' => 'timestamp', 'require' => false, 'desc' => '生日'],
            'sex' => ['name' => 'sex', 'type' => 'enum', 'range' => ['1', '2'], 'require' => false, 'desc' => '性别'],
        ];
        $rules['infoData'] = [
            'id' => ['name' => 'id', 'type' => 'int', 'require' => true, 'min' => 0, 'desc' => "查询ID"],
            'field' => ['name' => 'field', 'type' => 'string', 'default' => '*', 'desc' => '查询字段'],
        ];
        return $rules;
    }

    /**
     * 会员信息接口
     * @throws BadRequestException
     */
    public function infoData()
    {
        $user_id = $this->id;
        $domain_user = $this->Domain_User();
        if ($user_id == 0) {
            $user_info = $domain_user::$user;
        } else {
            $user_info = $domain_user::getInfo($user_id);
        }
        if (!$user_info) {
            throw new BadRequestException(T('没有找到该用户'));
        }
        $user_id = $user_info['id'];
        $user_info = $domain_user::getCurrentUserInfo($user_info);
        if (!$user_info) {
            throw new BadRequestException(T('没有找到该用户'));
        }
        $user_info['topic_count'] = self::getModel('Topic')->getCount(['user_id' => $user_id]);
        $user_info['reply_count'] = self::getModel('Reply')->getCount(['user_id' => $user_id]);
        return $user_info;
    }

    /**
     * 用户 逻辑层
     * @return \Common\Domain\User
     */
    protected function Domain_User()
    {
        return self::getDomain('User');
    }

    /**
     * 登录接口
     * @desc 根据账号和密码进行登录操作
     * @return array
     * @throws BadRequestException
     */
    public function signIn()
    {
        $data = get_object_vars($this);
        return $this->Domain_User()::doSignIn($data);
    }

    /**
     * 退出登录接口
     * @desc 退出登录接口
     */
    public function signOut()
    {
        $this->Domain_User()::doSignOut();
    }

    /**
     * 获取当前登录的会员信息
     * @return array
     */
    public function getCurrentUser()
    {
        return [
            'user' => $this->Domain_User()::getCurrentUserInfo(),
        ];
    }

    /**
     * 修改会员信息
     * @desc 修改会员信息
     * @throws BadRequestException
     */
    public function doInfo()
    {
        $update = [
            'id' => $this->id,
            'user_name' => $this->user_name,
            'nick_name' => $this->nick_name,
            'email' => $this->email,
            'real_name' => $this->real_name,
            'birth_time' => $this->birth_time,
            'sex' => $this->sex,
        ];
        if (!empty($this->password)) {
            $update['a_pwd'] = encrypt($this->password);
            $update['password'] = pwd_hash($this->password);
        }
        $this->Domain_User()::doUpdate($update);
    }

    /**
     * 注册接口
     * @desc 根据提供信息进行注册操作
     * @return array
     * @throws BadRequestException
     * @throws InternalServerErrorException
     */
    public function signUp()
    {
        $data = get_object_vars($this);
        return $this->Domain_User()::doSignUp($data);
    }

    /**
     * 获取性别数组
     * @return string|array
     */
    public function getSexName()
    {
        return $this->Domain_User()::getSexName();
    }
}
