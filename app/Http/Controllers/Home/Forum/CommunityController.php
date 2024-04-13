<?php

namespace App\Http\Controllers\Home\Forum;

use App\Http\Controllers\BaseController;
use App\Utils\Tools;
use App\Utils\ValidateRule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Response;

class CommunityController extends BaseController
{
    public function getRules()
    {
        return [
            'page' => [
                ...ValidateRule::listRule(),
                'pid' => ['desc' => '父级ID', 'int', 'exists' => [Tools::model()->ForumForumCommunity::class, 'pid']],
            ],
            'info' => [
                'id' => ['desc' => 'ID', 'int', 'required', 'exists' => [Tools::model()->ForumForumCommunity::class, 'id']],
            ],
        ];
    }

    /**
     * 列表
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function list()
    {
        $list = Tools::model()
            ->ForumForumCommunity
            ->where('pid', 0)
            ->where('level', 0)
            ->where('is_show', 1)
            ->orderBy('sort')
            ->orderBy('id')
            ->with(['children' => fn($query) => $query->where('is_show', 1)])
            ->get();
        return Response::api('', $list);
    }

    /**
     * 分页列表
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\Request\BadRequestException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function page()
    {
        $params = $this->getParams();
        $top_community = empty($params['pid']) ? null : Tools::model()->ForumForumCommunity
            ->whereInput('id', 'pid')
            ->select(['id', 'name'])
            ->first();
        $page = Tools::model()->ForumForumCommunity
            ->when($top_community, fn(Builder $query) => $query->where('pid', $top_community->id))
            ->where('is_show', 1)
            ->orderBy('sort')
            ->orderBy('id')
            ->getPage();
        if ($params['page'] == 1) $page['title'] = $top_community ? $top_community->name : '板块';
        return $this->success('', $page);
    }

    public function info()
    {
        $params = $this->getParams();
        $community = Tools::model()->ForumForumCommunity
            ->with([
                'topicTypes',
            ])
            ->find($params['id']);
        return $this->success('', $community);
    }
}
