<?php

namespace App\Http\Controllers\Home\Article;

use App\Exceptions\Request\BadRequestException;
use App\Models\Article\ArticleCategory;
use App\Utils\Tools;
use App\Utils\ValidateRule;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class CategoryController extends \App\Http\Controllers\BaseController
{
    protected function getRules()
    {
        return [
            'list' => [
                'id' => ['desc' => 'ID', 'exists' => [ArticleCategory::class, 'id']],
                'code' => ['desc' => '标识码', 'exists' => [ArticleCategory::class, 'code']],
            ],
            'page' => ValidateRule::listRule([
                'id' => ['desc' => 'ID', 'exists' => [ArticleCategory::class, 'id']],
                'code' => ['desc' => '标识码', 'exists' => [ArticleCategory::class, 'code']],
            ]),
            'info' => ValidateRule::listRule([
                'id' => ['desc' => 'ID', 'exists' => [ArticleCategory::class, 'id']],
                'code' => ['desc' => '标识码', 'exists' => [ArticleCategory::class, 'code']],
            ]),
        ];
    }

    /**
     * 列表
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function list()
    {
        $params = $this->getParams();
        $top_category = empty($params['id']) && empty($params['code']) ? null : Tools::model()->ArticleArticleCategory
            ->whereInput('id')
            ->whereInput('code')
            ->select(['id', 'title'])
            ->first();
        $list = Tools::model()->ArticleArticleCategory
            ->when($top_category, fn(Builder $query) => $query->where('pid', $top_category->id))
            ->where('is_show', 1)
            ->orderBy('sort')
            ->orderByDesc('id')
            ->with([
                'articles' => function ($query) {
                    $query
                        ->where('is_show', 1)
                        ->orderBy('sort')
                        ->orderByDesc('id');
                },
            ])
            ->get();
        if ($params['page'] == 1) $list['title'] = $top_category ? $top_category->title : '文章分类';
        return $this->success('', $list);
    }

    /**
     * 列表
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function page()
    {
        $params = $this->getParams();
        $top_category = empty($params['id']) && empty($params['code']) ? null : Tools::model()->ArticleArticleCategory
            ->whereInput('id')
            ->whereInput('code')
            ->select(['id', 'title'])
            ->first();
        $page = Tools::model()->ArticleArticleCategory
            ->when($top_category, fn(Builder $query) => $query->where('pid', $top_category->id))
            ->where('is_show', 1)
            ->orderBy('sort')
            ->orderByDesc('id')
            ->with([
                'articles' => function ($query) {
                    $query
                        ->where('is_show', 1)
                        ->orderBy('sort')
                        ->orderByDesc('id');
                },
            ])
            ->getPage();
        if ($params['page'] == 1) $page['title'] = $top_category ? $top_category->title : '文章分类';
        return $this->success('', $page);
    }

    public function info()
    {
        $params = $this->getParams();
        if (empty($params['id']) && empty($params['code'])) throw new BadRequestException('参数错误');
        $category = Tools::model()->ArticleArticleCategory
            ->when(!empty($params['id']), fn(Builder $query) => $query->where('id', $params['id']))
            ->when(!empty($params['code']), fn(Builder $query) => $query->where('code', $params['code']))
            ->first();
        return $this->success('', $category);
    }

}
