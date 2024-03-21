<?php

namespace App\Http\Controllers\Fund;

use App\Exceptions\Request\BadRequestException;
use App\Http\Controllers\BaseController;
use App\Utils\Tools;
use App\Utils\ValidateRule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;

class OptionalController extends BaseController
{
    public function getRules()
    {
        return [
            'page' => [
                ...ValidateRule::listRule(),
            ],
            'info' => [
                'id' => ['desc' => '基金产品ID', 'int', 'exists' => [$this->modelFundFundProduct::class, 'id']],
            ],
            'add' => [
                'fund_id' => ['desc' => '基金产品ID', 'int', 'exists' => [$this->modelFundFundProduct::class, 'id']],
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
        $page = Tools::model()->UserUserOptionalFund
            ->where('user_id', Tools::auth()->id('user'))
            ->with([
                'fund',
            ])
            ->orderBy('sort')
            ->getPage();
        return Response::api('', $page);
    }

    /**
     * 详情
     *
     * @return \Illuminate\Http\JsonResponse
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

    /**
     * 用户添加自选
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws BadRequestException
     * @throws \App\Exceptions\Request\UnauthorizedException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function add()
    {
        $user = Tools::auth()->user('user');
        $params = $this->getParams();
        $fund = Tools::model()->FundFundProduct->find($params['fund_id']);
        $exists_user_optional_fund = Tools::model()->UserUserOptionalFund->where('user_id', $user->id)->where('fund_id', $fund->id)->count();
        if ($exists_user_optional_fund) throw new BadRequestException('自选已存在');
        Tools::model()->UserUserOptionalFund->create([
            'fund_id' => $fund->id,
            'code' => $fund->code,
            'name' => $fund->name,
        ]);
        return Response::api('操作成功');
    }
}
