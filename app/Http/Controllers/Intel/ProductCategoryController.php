<?php

namespace App\Http\Controllers\Intel;

use App\Http\Controllers\BaseController;
use App\Models\Intel\IntelProductCategory;
use App\Models\System\SystemLanguage;
use App\Utils\ValidateRule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Response;

class ProductCategoryController extends BaseController
{
    public function getRules()
    {
        return [
            'page' => [
                ...ValidateRule::listRule(),
                'pid' => ['desc' => '父级分类ID', 'min' => 0, 'int', 'default' => 0],
                'language' => ['desc' => '语言', 'string', 'required', 'exists' => [$this->modelSystemSystemLanguage::class, 'key'], 'default' => 'zh_cn'],
                'tree' => ['desc' => '是否返回树形结构', 'bool', 'default' => false],
            ],
        ];
    }

    public function page()
    {
        $params = $this->getParams();
        $page = $this->modelIntelIntelProductCategory
            ->whereInput('pid')
            ->whereInput('language')
            ->orderBy('id')
            ->when($params['tree'], fn(Builder $query) => $query->with(['children']))
            ->getPage();
        return Response::api('', $page);
    }
}
