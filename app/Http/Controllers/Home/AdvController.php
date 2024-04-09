<?php

namespace App\Http\Controllers\Home;

use App\Exceptions\Request\BadRequestException;
use App\Http\Controllers\BaseController;
use App\Models\AdvCategory;
use Illuminate\Http\JsonResponse;

class AdvController extends BaseController
{
    protected function getRules()
    {
        $rules = [
            'list' => [
                'category_code' => ['desc' => '广告编码', 'required', 'exists' => [AdvCategory::class, 'code']],
            ],
        ];
        return $rules;
    }

    /**
     * 列表
     *
     * @return JsonResponse
     * @throws BadRequestException
     */
    public function list()
    {
        $params = $this->getParams();
        $adv_category = $this->modelAdvCategory
            ->select('id')
            ->where('code', $params['category_code'])
            ->with('advs:category_id,title,image,url')
            ->firstOrThrow();
        return response()->api('', $adv_category->advs);
    }

}
