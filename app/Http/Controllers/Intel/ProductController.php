<?php

namespace App\Http\Controllers\Intel;

use App\Http\Controllers\BaseController;
use App\Utils\ValidateRule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Response;

class ProductController extends BaseController
{
    public function getRules()
    {
        return [
            'page' => [
                ...ValidateRule::listRule(),
                'category_id' => ['desc' => '分类ID', 'int', 'exists' => [$this->modelIntelIntelProductCategory::class, 'id']],
                'series_id' => ['desc' => '系列ID', 'int', 'exists' => [$this->modelIntelIntelProductSeries::class, 'id']],
                'ark_series_id' => ['desc' => 'ARK系列ID', 'string', 'exists' => [$this->modelIntelIntelProductSeries::class, 'ark_series_id']],
                'language' => ['desc' => '语言', 'string', 'exists' => [$this->modelSystemSystemLanguage::class, 'key'], 'default' => 'zh_cn'],
            ],
            'info' => [
                'ark_product_id' => ['desc' => 'ARK产品ID', 'string', 'exists' => [$this->modelIntelIntelProduct::class, 'ark_product_id']],
                'language' => ['desc' => '语言', 'string', 'exists' => [$this->modelSystemSystemLanguage::class, 'key'], 'default' => 'zh_cn'],
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
        $page = $this->modelIntelIntelProduct
            ->whereInput('language')
            ->whereInput('ark_series_id')
            ->whereInput('category_id')
            ->whereInput('series_id')
            ->orderBy('id')
            ->getPage();
        return Response::api('', $page);
    }

    /**
     * 详情
     *
     * @return void
     */
    public function info()
    {
        $info = $this->modelIntelIntelProduct
            ->whereInput('language')
            ->whereInput('ark_product_id')
            ->with([
                'productSpecs',
            ])
            ->first();
        return Response::api('', $info);
    }
}
