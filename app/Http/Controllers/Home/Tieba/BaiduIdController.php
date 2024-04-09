<?php

namespace App\Http\Controllers\Home\Tieba;

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
            'del' => [
                'id' => ['int', 'required', 'exists' => [Tools::model()->TiebaBaiduId::class, 'id'], 'desc' => 'ID'],
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

    /**
     * 分页列表
     *
     * @return JsonResponse
     * @throws BadRequestException
     * @throws BindingResolutionException
     */
    public function page()
    {
        $params = $this->getParams();
        $page = Tools::model()->TiebaBaiduId
            ->getPage();
        return Response::api('', $page);
    }

    public function list()
    {
        // $params = $this->getParams();
        $list = Tools::model()->TiebaBaiduId
            ->select('id', 'name', 'bduss', 'stoken', 'portrait')
            ->withCount(['tieba'])
            ->get();
        return Response::api('', $list);
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
        $baidu_id = Misc::addBduss($params['bduss'], $params['stoken'] ?? null);
        return Response::api($baidu_id->wasRecentlyCreated ? '添加成功' : '修改成功', $baidu_id);
    }

    /**
     * 删除
     *
     * @return JsonResponse
     * @throws BadRequestException
     * @throws BindingResolutionException
     */
    public function del()
    {
        $params = $this->getParams();
        Tools::model()->TiebaBaiduId
            ->whereInput('id')
            ->delete();
        return Response::api('操作成功');
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
