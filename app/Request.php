<?php

namespace app;

// 应用请求对象类
use app\model\Admin;
use app\model\User;
use library\exception\BadRequestException;
use library\facade\Serialize;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Exception;

class Request extends \think\Request
{
    //region 身份令牌
    /**
     * @var string 管理员令牌
     */
    public $admin_token;

    /**
     * @var string 用户令牌
     */
    public $user_token;
    //endregion

    //region 管理员登陆状态
    protected $admin;

    /**
     * 设置管理员登录状态
     * @param  $admin
     */
    public function setAdmin($admin)
    {
        //将管理员信息存入SESSION中
        session(config('app.admin_token'), opensslEncrypt(Serialize::encrypt(['id' => $admin['id']])));// 保存在session
    }

    /**
     * 获取管理员登录状态
     * @return mixed
     */
    public function getAdmin()
    {
        return Serialize::decrypt(opensslDecrypt(session(config('app.admin_token')) ?? ''));// Session中的管理员信息
    }

    /**
     * 清除管理员登录状态
     */
    public function unsetAdmin()
    {
        session(config('app.admin_token'), null);
    }

    /**
     * 取得当前登录管理员
     * @param bool $thr
     * @return Admin|array|mixed|\think\Model
     * @throws DataNotFoundException
     * @throws DbException
     * @throws BadRequestException
     * @throws ModelNotFoundException
     */
    public function getCurrentAdmin(bool $thr = false)
    {
        if ($this->admin) return $this->admin;
        $admin = $this->getAdmin();// 获取Session中存储的管理员信息
        if ($admin) {
            $admin = (new Admin())->where('id', $admin['id'])->find();
        } else if (!empty($this->admin_token)) {
            $admin = (new Admin())->where('token', $this->admin_token)->find();// 用Token换取管理员信息
        }
        if ($admin) {
            $this->setAdmin($admin);
            $this->admin = $admin;
        } else if ($thr) {
            throw new BadRequestException('请登录', 2);
        }
        return $this->admin;
    }
    //endregion

    //region 用户登陆状态
    protected $user;

    /**
     * 设置用户登录状态
     * @param  $user
     */
    public function setUser($user)
    {
        //将用户信息存入SESSION中
        session(config('app.user_token'), opensslEncrypt(Serialize::encrypt(['id' => $user['id']])));// 保存在session
    }

    /**
     * 获取用户登录状态
     * @return mixed
     */
    public function getUser()
    {
        return Serialize::decrypt(opensslDecrypt(session(config('app.user_token')) ?? ''));// Session中的用户信息
    }

    /**
     * 清除管理员登录状态
     */
    public function unsetUser()
    {
        session(config('app.user_token'), null);
    }

    /**
     * 取得当前登录用户
     * @param bool $thr
     * @return User|array|mixed|\think\Model|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws BadRequestException
     * @throws ModelNotFoundException
     */
    public function getCurrentUser(bool $thr = false)
    {
        if ($this->user) return $this->user;
        $user = $this->getUser();// 获取Session中存储的用户信息
        if ($user) {
            $user = (new User())->where('id', $user['id'])->find();
        } else if (!empty($this->user_token)) {
            $user = (new User())->where('token', $this->user_token)->find();// 用Token换取用户信息
        }
        if ($user) {
            if (!empty(session('worker_client_id')) && $user->client_id != session('worker_client_id')) {
                $user->client_id = session('worker_client_id');
                $user->save();
            }
            $this->setUser($user);
            $this->user = $user;
        } else if ($thr) {
            throw new BadRequestException('请登录', 1);
        }
        return $this->user;
    }
    //endregion

}
