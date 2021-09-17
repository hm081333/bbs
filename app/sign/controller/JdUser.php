<?php
declare (strict_types=1);

namespace app\sign\controller;

use app\BaseController;

class JdUser extends BaseController
{
    public function listData()
    {
        // 开始位置
        $offset = $this->request->param('offset/d', 0);
        // 数量
        $limit = $this->request->param('limit/d', 10);
        // 查询字段
        $field = $this->request->param('field', '*');
        // 查询条件
        $where = $this->request->param('where', []);
        // 排序方式
        $order = $this->request->param('order', 'id desc');

        $user = $this->request->getCurrentUser(true);
        $where[] = ['user_id', '=', $user['id']];
        $total = $this->modelJdUser->where($where)->count();
        $rows = [];
        if ($total) {
            $rows = $this->modelJdUser
                ->with(['jd_sign' => function ($query) use ($user) {
                    $query
                        ->where([
                            ['user_id', '=', $user['id']],
                            ['status', '=', 1],
                        ])
                        ->field('jd_user_id,sign_key')
                        ->order('id asc');
                }])
                ->where($where)
                ->field($field)
                ->order($order)
                ->limit($offset, $limit)
                ->select();
            $rows = $rows
                ->append([
                    'status_name',
                ])
                ->map(function (\app\model\JdUser $row) {
                    $row->setRelation('sign_list', $row->jd_sign->column('sign_key'));
                    unset($row->jd_sign);
                    return $row;
                });
        }
        $sign_list = $this->modelJdSignItem
            ->append([
                'disabled'
            ])
            ->order('id asc')
            ->select();
        return success('', ['total' => $total, 'rows' => $rows, 'offset' => $offset, 'limit' => $limit, 'sign_list' => $sign_list]);
    }

    public function allListData()
    {
        $user = $this->request->getCurrentUser(true);
        $this->where[] = ['user_id', '=', $user['id']];
        return parent::allListData();
    }

}
