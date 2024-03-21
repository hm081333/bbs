<?php

namespace App\Http\Controllers\Fund;

use App\Http\Controllers\BaseController;
use App\Utils\Tools;
use App\Utils\ValidateRule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;

class ProductController extends BaseController
{
    public function getRules()
    {
        return [
            'list' => [
                'columns' => ['desc' => '字段', 'array'],
                'columns.*' => ['desc' => '字段', Rule::in(array_keys(Tools::model()->FundFundProduct_columns)),],
            ],
            'page' => [
                ...ValidateRule::listRule(),
                // 'language' => ['desc' => '语言', 'int', 'exists' => [$this->modelSystemSystemLanguage::class, 'key'], 'default' => 'zh_cn'],
            ],
            'info' => [
                'id' => ['desc' => '基金产品ID', 'int', 'exists' => [$this->modelFundFundProduct::class, 'id']],
                // 'language' => ['desc' => '语言', 'int', 'exists' => [$this->modelSystemSystemLanguage::class, 'key'], 'default' => 'zh_cn'],
            ],
        ];
    }

    /**
     * 列表
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\Request\BadRequestException
     */
    public function list()
    {
        $params = $this->getParams();
        $list = collect($this->modelFundFundProduct::getList());
        // $list = $this->modelFundFundProduct::get();
        if (!empty($params['columns'])) $list = $list->select($params['columns']);
        return Response::api('', $list);
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
        // dd($params['search_keyword']);
        $page = $this->modelFundFundProduct
            ->when(empty($params['search_field']) && !empty($params['search_keyword']), fn(Builder $query) => $query
                ->where(fn(Builder $query) => $query
                    ->whereLike('code', $params['search_keyword'], 'or')
                    ->whereLike('name', $params['search_keyword'], 'or')
                    ->whereLike('pinyin_initial', strtoupper($params['search_keyword']), 'or')))
            // ->orderByDesc('id')
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
        $info = $this->modelFundFundProduct
            ->whereInput('language')
            ->whereInput('ark_product_id')
            ->with([
                'productSpecs',
            ])
            ->first();
        return Response::api('', $info);
    }
}
