<?php
declare (strict_types=1);

namespace app\chat\controller;

use app\BaseController;
use library\exception\BadRequestException;
use think\Request;

class ChatMessage extends BaseController
{
    /**
     * 获取指定聊天室的聊天消息记录
     */
    public function chatMessageListData()
    {
        // 开始位置
        $offset = $this->request->param('offset/d', 0);
        // 数量
        $limit = $this->request->param('limit/d', 10);
        // 查询字段
        $field = $this->request->param('field', 'id,user_id,message,add_time');
        // 查询条件
        $where = $this->request->param('where', []);
        $order = $this->request->param('order', 'id desc');

        $user = $this->request->getCurrentUser(true);
        $list = [
            'total' => $this->modelChatMessage->where($where)->count(),
            'rows' => new \think\model\Collection(),
            'offset' => $offset,
            'limit' => $limit,
        ];
        if ($list['total'] <= 0) {
            throw new BadRequestException(T('没有更多消息'));
        }
        $list['rows'] = $this->modelChatMessage->where($where)->field($field)->order($order)->limit($offset, $limit)->select();

        $list['rows'] = $list['rows']->map(function (\app\model\ChatMessage $row) use ($user) {
            $message_user = $this->modelUser->where(['id' => $row['user_id']])->find();
            return [
                'message_id' => $row['id'],
                'message' => strToHtml($row['message']),
                'type' => $row['user_id'] == $user['id'] ? 'send' : 'receive',
                'user' => [
                    'user_id' => $message_user['id'],
                    'user_name' => $message_user['user_name'],
                    'nick_name' => $message_user['nick_name'],
                    'logo' => $message_user['logo'],
                ],
                'add_time' => $row['add_time'],
                // 'add_time_date' => $row['add_time_date'],
                // 'add_time_unix' => $row['add_time_unix'],
            ];
        });
        return success('', $list);
    }
}
