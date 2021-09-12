<?php
declare (strict_types=1);

namespace app\bbs\controller;

use app\BaseController;
use library\exception\BadRequestException;
use think\facade\Db;
use think\Request;

class Reply extends BaseController
{
    public function doInfo()
    {
        // 文章ID
        $topicId = $this->request->post('topicId/d');
        // 回复ID
        $id = $this->request->post('replyId/d', 0);
        // 回复内容
        $content = $this->request->post('content');

        $user = $this->request->getCurrentUser(true);// 当前登录的会员
        $topic = $this->modelTopic->where(['id' => $topicId])->find();
        if (!$topic) throw new BadRequestException('文章不存在');
        $reply = $this->modelReply->where(['id' => $id])->findOrEmpty();
        if ($reply->isEmpty()) {
            $reply_sort = $this->modelReply->where(['sort' => $topicId])->max('sort');
            $reply['sort'] = $reply_sort + 1;
            // 更新回复数量
            $topic['reply'] = Db::raw('reply + 1');
            $topic->save();
        }

        $reply['topic_id'] = $topic['id'];
        $reply['user_id'] = $user['id'];
        $reply['name'] = $user['user_name'];
        $reply['email'] = $user['email'];
        $reply['detail'] = $content;
        $reply->save();
        return success('操作成功');
    }

}
