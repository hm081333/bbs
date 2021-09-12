<?php
declare (strict_types=1);

namespace app\admin\controller;

use app\BaseController;
use think\Request;

class Subject extends BaseController
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
        //
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
     * 修改课程信息
     * @desc 修改课程信息
     * @param int $id
     * @return \think\response\Json
     */
    public function update($id = 0)
    {
        $data = $this->request->post();
        $subject = $this->modelSubject->where('id', $id)->findOrEmpty();
        $subject->name = $data['name'];
        $subject->tips = $data['tips'];
        $subject->status = $data['status'];
        $subject->save();
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
