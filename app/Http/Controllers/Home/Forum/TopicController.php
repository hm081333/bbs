<?php

namespace App\Http\Controllers\Home\Forum;

use App\Http\Controllers\BaseController;
use App\Utils\Tools;
use App\Utils\ValidateRule;

class TopicController extends BaseController
{
    public function getRules()
    {
        return [
            'page' => [
                ...ValidateRule::listRule(),
                'community_id' => ['desc' => '板块ID', 'int', 'exists' => [Tools::model()->ForumForumCommunity::class, 'id']],
                'topic_type_id' => ['desc' => '主题分类ID', 'int', 'exists' => [Tools::model()->ForumForumTopicType::class, 'id']],
            ],
            'add' => [
                'user_id' => ['desc' => '用户ID', 'int', 'exists' => [Tools::model()->UserUser::class, 'id'], 'default' => fn() => Tools::auth()->id('user')],
                'forum_community_id' => ['desc' => '版块ID', 'int', 'required', 'exists' => [Tools::model()->ForumForumCommunity::class, 'id']],
                'forum_topic_type_id' => ['desc' => '主题分类ID', 'int', 'required', 'exists' => [Tools::model()->ForumForumTopicType::class, 'id']],
                'title' => ['desc' => '主题标题', 'string', 'required'],
                'content' => ['desc' => '主题内容', 'string', 'required'],
            ],
            'info' => [
                'id' => ['desc' => '主题ID', 'int', 'exists' => [Tools::model()->ForumForumTopic::class, 'id']],
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
        $page = Tools::model()->ForumForumTopic
            ->whereInput('forum_community_id', 'community_id')
            ->whereInput('forum_topic_type_id', 'topic_type_id')
            ->where('is_show', 1)
            ->orderBy('sort')
            ->orderByDesc('id')
            ->with(['topicType'])
            ->getPage();
        return $this->success('', $page);
    }

    public function add()
    {
        $params = $this->getParams();
        $modelForumTopic = Tools::model()->ForumForumTopic->create($params);
        return $this->success('发表成功', $modelForumTopic);
    }

    public function info()
    {
        $params = $this->getParams();
        $topic = Tools::model()->ForumForumTopic
            // ->with([
            //     'replies',
            // ])
            ->with(['topicType', 'user:id,user_name'])
            ->find($params['id']);
        return $this->success('', $topic);
    }
}
