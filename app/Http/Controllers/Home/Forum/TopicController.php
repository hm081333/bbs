<?php

namespace App\Http\Controllers\Home\Forum;

use App\Http\Controllers\BaseController;
use App\Utils\Tools;
use App\Utils\ValidateRule;
use Illuminate\Database\Eloquent\Builder;

class TopicController extends BaseController
{
    public function getRules()
    {
        return [
            'page' => [
                ...ValidateRule::listRule(),
                'community_id' => ['desc' => '板块ID', 'int', 'exists' => [Tools::model()->ForumForumCommunity::class, 'id']],
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
        $community = empty($params['community_id']) ? null : Tools::model()->ForumForumCommunity
            ->whereInput('id', 'community_id')
            ->select(['id', 'name'])
            ->first();
        $page = Tools::model()->ForumForumTopic
            ->when($community, fn(Builder $query) => $query->where('forum_community_id', $community->id))
            ->where('is_show', 1)
            ->orderBy('sort')
            ->orderByDesc('id')
            ->getPage();
        if ($params['page'] == 1) $page['title'] = $community ? $community->name : '主题';
        return $this->success('', $page);
    }
}
