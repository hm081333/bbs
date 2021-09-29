<?php
declare (strict_types=1);

namespace app\chat\controller;

use app\BaseController;
use think\Request;

class User extends BaseController
{
    /**
     * 修改昵称
     * @param string $nick_name 更新的名称
     * @return \think\response\Json
     * @throws \library\exception\BadRequestException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function editNickName($nick_name = '')
    {
        $user = $this->request->getCurrentUser(true);
        $user->nick_name = $nick_name;
        $user->save();
        return success('操作成功', $user->getUserInfo());
    }

    /**
     * 修改性别
     * @param $sex string|int 性别
     * @return \think\response\Json
     * @throws \library\exception\BadRequestException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function editGender($sex)
    {
        $user = $this->request->getCurrentUser(true);
        $user->sex = $sex;
        $user->save();
        return success('操作成功', $user->getUserInfo());
    }

    /**
     * 修改个性签名
     * @param string $signature
     * @return \think\response\Json
     * @throws \library\exception\BadRequestException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function editSignature($signature = '')
    {
        $user = $this->request->getCurrentUser(true);
        $user->signature = $signature;
        $user->save();
        return success('操作成功', $user->getUserInfo());
    }

    /**
     * 修改个人头像
     * @param string $logo
     * @return \think\response\Json
     * @throws \library\exception\BadRequestException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function editLogo($logo = '')
    {
        $user = $this->request->getCurrentUser(true);
        $user->logo = $logo;
        $user->save();
        return success('操作成功', $user->getUserInfo());
    }
}
