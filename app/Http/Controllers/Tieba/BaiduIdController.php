<?php

namespace App\Http\Controllers\Tieba;

use App\Exceptions\Request\BadRequestException;
use App\Exceptions\Request\UnauthorizedException;
use App\Http\Controllers\BaseController;
use App\Utils\TieBa\Misc;
use App\Utils\Tools;
use App\Utils\ValidateRule;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class BaiduIdController extends BaseController
{

    public function getRules()
    {
        return [
            'page' => [
                ...ValidateRule::listRule(),
            ],
            'store' => [
                'bduss' => ['string', 'required', 'desc' => "BDUSS"],
                'stoken' => ['string', 'desc' => "STOKEN"],
            ],
            'getLoginQrcode' => [],
            'qrLogin' => [
                'sign' => ['string', 'required', 'desc' => '签名'],
            ],
        ];
    }

    public function page()
    {
        $params = $this->getParams();
        $page = Tools::model()->TiebaBaiduId
            ->getPage();
        return Response::api('', $page);
    }

    /**
     * 手动添加或更新BDUSS
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws BindingResolutionException
     */
    public function store()
    {
        $params = $this->getParams();
        return Response::api('添加成功', Misc::addBduss($params['bduss'], $params['stoken'] ?? null));
    }

    /**
     * 获得登录二维码及sign
     *
     * @return JsonResponse
     */
    public function getLoginQrcode()
    {
        return Response::api('', Misc::getLoginQrcode());
    }

    /**
     * 二维码登录
     *
     * @return mixed
     * @throws BadRequestException
     */
    public function qrLogin()
    {
        $params = $this->getParams();
        return Response::api('', Misc::qrLogin($params['sign']));
    }

}
