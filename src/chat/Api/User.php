<?php

namespace Chat\Api;

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
        return $rules;
    }

    /**
     * 修改昵称
     * @throws \Library\Exception\BadRequestException
     */
    public function editNickName()
    {
        $user = $this->Domain_User()::getCurrentUser(true);
        $this->Domain_User()->editUser($user['id'], ['nick_name' => $this->nick_name]);
        $user = $this->Domain_User()::getCurrentUser(true);
        var_dump($user);
    }

}
