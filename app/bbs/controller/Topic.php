<?php
declare (strict_types=1);

namespace app\bbs\controller;

use app\BaseController;
use library\exception\BadRequestException;
use library\exception\InternalServerErrorException;
use think\facade\Db;
use think\Request;

class Topic extends BaseController
{
    /**
     * 文章列表
     * @desc 文章列表
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function listData()
    {
        $class_id = $this->request->post('class_id/d', 0);
        $where = [];
        $class = [];
        if ($class_id > 0) {
            $class = $this->modelSubject->where(['id' => $class_id])->find();
            $where['class_id'] = $class['id'];
        }
        // 开始位置
        $offset = $this->request->param('offset/d', 0);
        // 数量
        $limit = $this->request->param('limit/d', 10);
        // 查询条件
        $where = $this->request->param('where', []);
        $total = $this->modelTopic->where($where)->count();
        $rows = [];
        if ($total) {
            $rows = $this->modelTopic->where($where)->field('id,title,add_time')->order('id desc')->limit($offset, $limit)->select();
        }
        return success('', ['total' => $total, 'rows' => $rows, 'offset' => $offset, 'limit' => $limit, 'subject_name' => $class['name'] ?? '']);
    }

    /**
     * 文章详情数据
     * @desc      获取文章详情数据
     * @return \think\response\Json    数据数组
     * @throws BadRequestException
     */
    public function InfoData()
    {
        $id = $this->request->post(['id']);
        $topic = $this->modelTopic->where(['id' => $id])->find();
        if (!$topic) throw new BadRequestException('文章不存在');
        // 浏览数+1
        $topic->inc('view', 1)->update();
        return success('', $topic);
    }

    /**
     * 新建文章
     * @return \think\response\Json
     * @throws InternalServerErrorException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function create()
    {
        // 顶置
        $sticky = $this->request->post('sticky', 0);
        // if (empty($data['title'])) {
        //     throw new PhalApi_Exception_Error(T('请输入文章标题'), 1);// 抛出普通错误 T标签翻译
        // } else if (empty($data['content'])) {
        //     throw new PhalApi_Exception_Error(T('请输入正文内容'), 1);// 抛出普通错误 T标签翻译
        // } else if (empty($data['subject_id'])) {
        //     throw new PhalApi_Exception_Error(T('请选择课程'), 1);// 抛出普通错误 T标签翻译
        // }
        // 当前登录的会员
        $user = $this->request->getCurrentUser(true);
        $topic_model = $this->modelTopic;
        $topic_model->appendData([
            // 课程ID
            'class_id' => $this->request->post('subject_id/d'),
            // 文章标题
            'title' => $this->request->post('title'),
            // 正文内容
            'detail' => $this->request->post('content'),
            'user_id' => $user['id'],
            'name' => $user['user_name'],
            'email' => $user['email'],
            // 'sticky' => $sticky,
        ]);
        $topic_model->save();
        $topic_id = $topic_model['id'];
        if ($topic_id) {
            return success('发布成功', ['topic_id' => $topic_id]);
        } else {
            throw new InternalServerErrorException(T('发布失败'), 2);// 抛出服务端错误
        }
    }
}
