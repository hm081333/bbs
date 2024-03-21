<?php

namespace App\Http\Controllers\Intel;

use App\Http\Controllers\BaseController;
use App\Utils\ValidateRule;
use Illuminate\Support\Facades\Response;

class ProductSeriesController extends BaseController
{
    public function getRules()
    {
        return [
            'page' => [
                ...ValidateRule::listRule(),
                'category_id' => ['desc' => '分类ID', 'int', 'exists' => [$this->modelIntelIntelProductCategory::class, 'id']],
                'category_panel_key' => ['desc' => '分类标识码', 'string', 'exists' => [$this->modelIntelIntelProductCategory::class, 'panel_key']],
                'language' => ['desc' => '语言', 'string', 'exists' => [$this->modelSystemSystemLanguage::class, 'key'], 'default' => 'zh_cn'],
            ],
        ];
    }

    public function page()
    {
        $params = $this->getParams();
        $page = $this->modelIntelIntelProductSeries
            ->whereInput('language')
            ->whereInput('category_panel_key')
            ->whereInput('category_id')
            ->orderByDesc('ark_series_id')
            ->getPage();
        return Response::api('', $page);
    }
}
