<?php

namespace App\Http\Controllers\Home\Forum;

use App\Http\Controllers\BaseController;
use App\Utils\Tools;
use App\Utils\ValidateRule;

class ReplyController extends BaseController
{
    public function getRules()
    {
        return [
            'page' => [
                ...ValidateRule::listRule(),
                'topic_id' => ['desc' => '主题ID', 'int', 'required', 'exists' => [Tools::model()->ForumForumTopic::class, 'id']],
            ],
            'add' => [
                'user_id' => ['desc' => '用户ID', 'int', 'exists' => [Tools::model()->UserUser::class, 'id'], 'default' => fn() => Tools::auth()->id('user')],
                'forum_topic_id' => ['desc' => '主题ID', 'int', 'required', 'exists' => [Tools::model()->ForumForumTopic::class, 'id']],
                'content' => ['desc' => '主题内容', 'string', 'required'],
            ],
        ];
    }

    /**
     * 分页列表
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\Request\BadRequestException
     */
    public function page()
    {
        $params = $this->getParams();
        $page = Tools::model()->ForumForumReply
            ->whereInput('forum_topic_id', 'topic_id')
            ->where('is_show', 1)
            ->orderBy('sort')
            ->orderBy('id')
            ->with(['user:id,user_name'])
            ->getPage();
        return $this->success('', $page);
    }

    public function add()
    {
        $params = $this->getParams();
        $modelForumTopic = Tools::model()->ForumForumReply->create($params);
        return $this->success('发表成功', $modelForumTopic->load(['user:id,user_name']));
    }

}
