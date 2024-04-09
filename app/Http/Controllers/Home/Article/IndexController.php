<?php

namespace App\Http\Controllers\Home\Article;

use App\Exceptions\Request\BadRequestException;
use App\Http\Controllers\BaseController;
use App\Models\Article\Article;
use App\Models\Article\ArticleCategory;
use App\Utils\Tools;
use App\Utils\ValidateRule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class IndexController extends BaseController
{
    protected function getRules()
    {
        $rules = [
            'page' => ValidateRule::listRule([
                'category_id' => ['desc' => '分类ID', 'exists' => [ArticleCategory::class, 'id']],
                'category_code' => ['desc' => '分类编码', 'exists' => [ArticleCategory::class, 'code']],
            ]),
            'info' => [
                'id' => ['desc' => 'ID', 'exists' => [Article::class, 'id']],
                'code' => ['desc' => '标识码', 'exists' => [Article::class, 'code']],
            ],
        ];
        return $rules;
    }

    /**
     * 分页
     *
     * @return JsonResponse
     * @throws BadRequestException
     */
    public function page()
    {
        $params = $this->getParams();
        $category = empty($params['category_code']) && empty($params['category_id']) ? null : Tools::model()->ArticleArticleCategory
            ->when(!empty($params['category_id']), fn(Builder $query) => $query->where('id', $params['category_id']))
            ->when(!empty($params['category_code']), fn(Builder $query) => $query->where('code', $params['category_code']))
            ->select(['id', 'title'])
            ->first();
        if (!$category) throw new BadRequestException('异常参数');
        $page = Tools::model()->ArticleArticle
            ->where('is_show', 1)
            ->where('category_id', $category->id)
            ->orderBy('sort')
            ->orderByDesc('id')
            ->select([
                'id',
                'title',
                'desc',
                'cover',
                'content',
            ])
            ->getPage();
        if ($params['page'] == 1) {
            $page['title'] = $category->title;
        }
        return $this->success('', $page);
    }

    /**
     * 详情
     *
     * @return JsonResponse
     * @throws BadRequestException
     */
    public function info()
    {
        $params = $this->getParams();
        if (!isset($params['id']) && !isset($params['code'])) throw new BadRequestException('参数错误');
        $info = Tools::model()->ArticleArticle
            ->where('is_show', 1)
            ->when(isset($params['id']), function ($query) use ($params) {
                $query->whereInput('id');
            }, function ($query) use ($params) {
                $query->whereInput('code');
            })
            ->select([
                'title',
                'content',
            ])
            ->firstOrThrow();
        return $this->success('', $info);
    }

}
