<?php
declare (strict_types=1);

namespace app\chat\controller;

use app\BaseController;
use library\exception\BadRequestException;
use Overtrue\Pinyin\Pinyin;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Request;

class Friend extends BaseController
{
    /**
     * 列表数据
     * @desc      获取列表数据
     * @return \think\response\Json    数据列表
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @exception 400 非法请求，参数传递错误
     */
    public function listData()
    {
        // 查询字段
        $field = $this->request->param('field', '*');
        // 查询条件
        $where = $this->request->param('where', []);

        $user = $this->request->getCurrentUser(true);
        $pinyin = new Pinyin();
        $where['user_id'] = $user['id'];
        $list = $this->modelFriend->with([
            'friend',
        ])->field('friend_id')->where($where)->select();
        $list = $list->map(function (\app\model\Friend $row) use ($pinyin) {
            return [
                'user_id' => $row->friend['id'],
                'nick_name' => $row->friend['nick_name'],
                'logo' => $row->friend['logo'],
                'pinyin' => $pinyin->abbr($row->friend['nick_name'], PINYIN_NO_TONE | PINYIN_KEEP_NUMBER | PINYIN_KEEP_ENGLISH),
            ];
        });
        return success('', $list);
    }


    /**
     * 好友状态
     * @param $user_id
     * @param $friend_id
     * @return int
     */
    private function friendStatus($user_id, $friend_id)
    {
        $friend_user = $this->modelFriend->where([
            'friend_id' => $user_id,
            'user_id' => $friend_id,
        ])->field('id')->find();
        $user_friend = $this->modelFriend->where([
            'user_id' => $user_id,
            'friend_id' => $friend_id,
        ])->field('id')->find();
        if (!$friend_user && !$user_friend) {
            $status = 0;
        } else if ($friend_user && !$user_friend) {
            $status = 1;
        } else if (!$friend_user && $user_friend) {
            $status = 2;
        } else if ($friend_user && $user_friend) {
            $status = 3;
        }
        return $status;
    }

    /**
     * 好友信息
     * @desc      获取详情数据
     * @return \think\response\Json    数据数组
     * @exception 400 非法请求，参数传递错误
     */
    public function infoData()
    {
        $friend_id = $this->request->post('id');
        $user = $this->request->getCurrentUser(true);
        $friend = $this->modelUser->where('id', $friend_id)->find();
        if (empty($friend)) {
            throw new BadRequestException(T('无法找到该用户'));
        }
        $status = $this->friendStatus($user['id'], $friend['id']);
        $status_name = $this->modelFriend->friendStatusName($status);
        $chat_id = $this->modelChat->whereRaw('FIND_IN_SET(?, `user_ids`) AND FIND_IN_SET(?, `user_ids`)', [$user['id'], $friend['id']])->field('id')->select();
        if (empty($chat_id)) {
            $chat_id = $this->modelChat->insert([
                'user_ids' => $user['id'] . ',' . $friend['id'],
                'add_time' => $this->request->time(),
                'edit_time' => $this->request->time(),
                'last_time' => $this->request->time(),
            ]);
        } else {
            $chat_id = $chat_id[0]['id'];
        }
        // var_dump($chat_id);
        return success('', [
            'status' => $status,
            'statusName' => $status_name,
            'chat_id' => $chat_id,
            'friendInfo' => [
                'id' => $friend['id'],
                'logo' => $friend['logo'],
                'nick_name' => $friend['nick_name'],
                'user_name' => $friend['user_name'],
            ],
        ]);
    }

}
