<?php
declare (strict_types=1);

namespace app\chat\controller;

use app\BaseController;
use library\exception\BadRequestException;

class Chat extends BaseController
{
    /**
     * 聊天室列表
     * @return array|mixed
     * @throws BadRequestException
     */
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

        $user = $this->request->getCurrentUser(true);
        $where[] = ['user_ids', 'find in set', $user['id']];
        $order = 'last_time DESC';
        $total = $this->modelChat->where($where)->count();
        $rows = [];
        if ($total) {
            $rows = $this->modelChat->where($where)->field($field)->order($order)->limit($offset, $limit)->select();
            $rows = $rows->map(function (\app\model\Chat $row) use ($user) {
                // 聊天室信息
                $chat_info = $row;
                // 最后消息时间
                $last_time = $chat_info['last_time_unix'] ?? $chat_info['last_time'];
                $row = [
                    'id' => $chat_info['id'],
                ];
                if ($chat_info['is_group']) {
                    // 群聊名称
                    $row['name'] = $chat_info['name'];
                    $row['logo'] = '';
                } else {
                    $friend_info = $this->modelFriend
                        ->field('friend_id,remark_name')
                        ->where([
                            ['user_id', '=', $user['id']],
                        ])
                        ->whereRaw("FIND_IN_SET(friend_id, :ids)", ['ids' => $chat_info['user_ids']])
                        ->find();
                    $friend_remark_name = $friend_info['remark_name'];
                    $friend_info = $this->modelUser->where(['id' => $friend_info['friend_id']])->cache()->find();
                    $row['name'] = empty($friend_remark_name) ? $friend_info['nick_name'] : $friend_remark_name;
                    $row['logo'] = empty($friend_info['logo']) ? '' : res_path($friend_info['logo']);

                }
                $last_message = $this->modelChatMessage
                    ->field('message')
                    ->where(['chat_id' => $chat_info['id']])
                    ->order('id desc')
                    ->limit(0, 1)
                    ->select();
                $row['last_message'] = $last_message[0]['message'] ?? '';
                $row['last_message'] = strToHtml($row['last_message']);
                if ($warp = strpos($row['last_message'], '<br/>')) {
                    $row['last_message'] = substr($row['last_message'], 0, $warp);
                }
                // 最后消息时间
                $row['last_time_short'] = sortTime($last_time);
                return $row;
            });
        }
        return success('', [
            'total' => $total,
            'rows' => $rows,
            'offset' => $offset,
            'limit' => $limit,
        ]);
    }

    /**
     * 聊天室信息
     * @desc      获取详情数据
     * @param int $id
     * @return \think\response\Json    数据数组
     * @throws BadRequestException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @exception 400 非法请求，参数传递错误
     */
    public function infoData()
    {
        $id = $this->request->post('id/d');
        $user = $this->request->getCurrentUser(true);
        $chat_info = $this->modelChat->where(['id' => $id])->cache()->find();
        if (!$chat_info) throw new BadRequestException(T('不存在该聊天室'));
        if ($chat_info['is_group']) {
            // 群聊名称
            // $chat_info['logo'] = '';
        } else {
            $friend_info = $this->modelFriend
                ->field('friend_id,remark_name')
                ->where([
                    ['user_id', '=', $user['id']],
                ])
                ->whereRaw("FIND_IN_SET(friend_id, :ids)", ['ids' => $chat_info['user_ids']])
                ->find();
            $friend_info = $this->modelUser->where(['id' => $friend_info['friend_id']])->cache()->find();
            $chat_info['name'] = empty($friend_info['remark_name']) ? $friend_info['nick_name'] : $friend_info['remark_name'];
            // $chat_info['logo'] = empty($friend_info['logo']) ? '' : res_path($friend_info['logo']);
        }
        return success('', $chat_info);
    }
}
