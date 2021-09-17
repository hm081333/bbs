<?php
declare (strict_types=1);

namespace app\common\controller;

use app\BaseController;
use library\exception\BadRequestException;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Exception;
use think\Request;
use think\response\Json;

class User extends BaseController
{
    /**
     * 获取当前登录的会员信息
     * @return Json
     * @throws BadRequestException
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getCurrentUser()
    {
        return success('', [
            'user' => $this->request->getCurrentUser(),
        ]);
    }

    /**
     * 会员信息接口
     * @return Json
     * @throws BadRequestException
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function infoData()
    {
        $user_id = $this->request->post('id');
        if ($user_id == 0) {
            $user = $this->request->getCurrentUser();
        } else {
            $user = $this->modelUser->where('id', $user_id)->find();
        }
        if (!$user) {
            throw new BadRequestException(T('没有找到该用户'));
        }
        $user = $user->getUserInfo();
        if (!$user) {
            throw new BadRequestException(T('没有找到该用户'));
        }
        $user['topic_count'] = $this->modelTopic->where(['user_id' => $user['id']])->count();
        $user['reply_count'] = $this->modelReply->where(['user_id' => $user['id']])->count();
        return success('', $user);
    }

    /**
     * 用户登录
     * @return Json
     * @throws BadRequestException
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function signIn()
    {
        $user_name = $this->request->post('user_name');   // 账号参数
        $password = $this->request->post('password');   // 密码参数
        $remember = $this->request->post('remember');   // 记住登录
        if (empty($user_name)) {
            throw new BadRequestException('请输入账号');
        } else if (empty($password)) {
            throw new BadRequestException('请输入密码');
        }
        $user = $this->modelUser->where('user_name', $user_name)->find();
        if (!$user) {
            throw new BadRequestException('用户不存在');
        } else if (!pwd_verify($password, $user['password'])) {
            throw new BadRequestException('密码错误');
        }
        if (empty($user->a_pwd)) {
            $user->a_pwd = opensslEncrypt($password);
        }
        if ($remember) {
            $user->token = md5(config('app.user_token') . md5(uniqid((string)mt_rand())));
        } else {
            $user->token = '';
        }
        $user->save();
        // 将用户信息存入SESSION中
        $this->request->setUser($user);
        return success('登陆成功', [
            'user' => $user->getUserInfo(),
            'token' => $user->token,
        ]);
    }

    /**
     * 用户登出
     * @return Json
     * @throws BadRequestException
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function signOut()
    {
        $user = $this->request->getCurrentUser(true);
        $user->client_id = '';
        $user->save();
        $this->request->unsetUser();
        return success('退出成功');
    }

    /**
     * 用户注册
     * @return Json
     * @throws BadRequestException
     */
    public function signUp()
    {
        $data = $this->request->post();
        $user_name = $data['user_name'];   // 账号参数
        // $password = $data['password'];   // 密码参数
        $email = $data['email'];   // 邮箱参数
        // $real_name = $data['real_name'];   // 姓名参数
        // $birth = $data['birth'];   // 生日参数
        // $sex = $data['sex'];   // 性别参数
        $user = $this->modelUser->where('user_name', $user_name)->findOrEmpty();
        if (!$user->isEmpty()) throw new BadRequestException('用户名已注册');
        $checkEmail = $this->modelUser->where('email', $email)->count('id');
        if ($checkEmail) throw new BadRequestException('邮箱已注册');

        $insert_data = [
            'user_name' => $data['user_name'],
            'password' => $data['password'],
            'email' => $data['email'],
            'real_name' => $data['real_name'],
            'birth_time' => substr($data['birth_time'], 0, 10),
            'sex' => $data['sex'],
            'status' => 1,
        ];
        $insert_data['a_pwd'] = opensslEncrypt($insert_data['password']);
        $insert_data['password'] = pwd_hash($insert_data['password']);
        $user->appendData($insert_data);

        //将用户信息存入SESSION中
        $this->request->setUser($user);
        return success('注册成功', [
            'user' => $user->toArray(),
        ]);
    }

}
