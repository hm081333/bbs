<?php
declare (strict_types=1);

namespace app\admin\controller;

use app\BaseController;
use think\Exception;
use think\Request;

class User extends BaseController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param \think\Request $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param int $id
     * @return \think\Response
     */
    public function read($id)
    {
        if ($id == 0) {
            $user_info = $this->request->getCurrentUser();
            $id = $user_info['id'];
        }
        $user_info = $this->modelUser->withCount([
            'topic',
            'reply',
        ])->where('id', $id)->find();
        if (!$user_info) {
            throw new Exception('没有找到该用户');
        }
        return success('', $user_info->toArray());
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param int $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 修改用户信息
     * @param $user_id
     * @return \think\response\Json
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function update($user_id)
    {
        if (empty($user_id)) {
            $user = $this->request->getCurrentUser(true);
            $user_id = $user['id'];
        }
        $user = $this->modelUser->where('id', $user_id)->find();
        $data = $this->request->post();
        $user->appendData($data);
        $user->save();

        return success('操作成功');
    }

    /**
     * 删除指定资源
     *
     * @param int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }

}
