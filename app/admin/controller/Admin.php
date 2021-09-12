<?php
declare (strict_types=1);

namespace app\admin\controller;

use app\BaseController;
use app\Request;
use library\facade\Serialize;
use think\Exception;

class Admin extends BaseController
{
    /**
     * 保存更新的资源
     * @param int $id
     * @return \think\Response
     */
    public function update($id = 0)
    {
        $this->modelAdmin->transaction(function () use ($id) {
            $post_data = $this->request->post();
            $admin = $this->modelAdmin->where('id', $id)->findOrEmpty();
            $admin->user_name = $post_data['user_name'];
            $admin->status = $post_data['status'];
            $password = $post_data['password'];
            if (!empty($password)) {
                $admin->a_pwd = opensslEncrypt($password);
                $admin->password = pwd_hash($password);
            }
            $admin->save();
        });
        return success('操作成功');
    }

    /**
     * 管理员登录
     * @return array|\think\Response
     * @throws Exception
     */
    public function signIn()
    {
        $post_data = $this->request->post();
        $user_name = $post_data['user_name'];   // 账号参数
        $password = $post_data['password'];   // 密码参数
        $remember = $post_data['remember'];   // 记住登录
        if (empty($user_name)) {
            throw new Exception('请输入账号');
        } else if (empty($password)) {
            throw new Exception('请输入密码');
        }
        $admin = $this->modelAdmin->where('user_name', $user_name)->findOrEmpty();
        if ($admin->isEmpty()) {
            throw new Exception('用户不存在');
        } else if (!pwd_verify($password, $admin->password)) {
            throw new Exception('密码错误');
        }
        $admin->token = '';
        if (empty($admin['a_pwd'])) {
            $admin->a_pwd = opensslEncrypt($password);;
        }
        if ($remember) {
            $admin->token = md5(config('app.admin_token') . md5(uniqid(mt_rand())));
        } else {
            $admin->token = '';
        }
        $admin->save();
        //将用户信息存入SESSION中
        $this->request->setAdminToken($admin);
        return success('登陆成功', [
            'admin' => $admin->toArray(),
            'token' => $admin->token,
        ]);
    }

    /**
     * 获取当前登录的管理员信息
     * @return \think\response\Json
     */
    public function getCurrentAdmin()
    {
        return success('', [
            'admin' => $this->request->getCurrentAdmin()->toArray(),
        ]);
    }

    /**
     * 管理员登出
     * @return \think\response\Json
     */
    public function signOut()
    {
        $this->request->clearAdminToken();
        return success('退出成功');
    }

}
