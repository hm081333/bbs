<?php

namespace App\Http\Controllers\Home\Tieba;

use App\Exceptions\Request\BadRequestException;
use App\Exceptions\Request\UnauthorizedException;
use App\Exceptions\Server\InternalServerErrorException;
use App\Http\Controllers\BaseController;
use App\Utils\TieBa\Misc;
use App\Utils\Tools;
use App\Utils\ValidateRule;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class TiebaController extends BaseController
{
    public function getRules()
    {
        return [
            'page' => [
                ...ValidateRule::listRule(),
                'baidu_id' => ['int', 'exists' => [Tools::model()->TiebaBaiduId::class, 'id'], 'desc' => '百度账号ID'],
            ],
            'del' => [
                'id' => ['int', 'required', 'exists' => [Tools::model()->TiebaBaiduTieba::class, 'id'], 'desc' => 'ID'],
            ],
            'refreshTieBa' => [
                'baidu_id' => ['int', 'required', 'exists' => [Tools::model()->TiebaBaiduId::class, 'id'], 'desc' => '百度账号ID'],
            ],
            'doSignByTieBaId' => [
                'id' => ['int', 'required', 'exists' => [Tools::model()->TiebaBaiduTieba::class, 'id'], 'desc' => 'tieba表的ID'],
            ],
            'doSignByBaiDuId' => [
                'baidu_id' => ['int', 'required', 'exists' => [Tools::model()->TiebaBaiduId::class, 'id'], 'desc' => '百度账号ID--签到该bduss所有贴吧'],
            ],
            'noSignTieBa' => [
                'id' => ['int', 'required', 'exists' => [Tools::model()->TiebaBaiduTieba::class, 'id'], 'desc' => 'tieba表的ID'],
                // 'no' => ['string', 'in' => ['0', '1'], 'required', 'desc' => '是否忽略签到'],
                'no' => ['boolean', 'required', 'desc' => '是否忽略签到'],
            ],
        ];
    }

    public function page()
    {
        $params = $this->getParams();
        $page = Tools::model()->TiebaBaiduTieba
            ->whereInput('baidu_id')
            ->whereUserId()
            // ->orderBy('baidu_id')
            ->orderBy('id')
            ->getPage();
        return Response::api('', $page);
    }

    public function del()
    {
        $params = $this->getParams();
        Tools::model()->TiebaBaiduTieba
            ->whereInput('id')
            ->delete();
        return Response::api('操作成功');
    }

    /**
     * 刷新贴吧列表
     *
     * @throws InternalServerErrorException
     */
    public function refreshTieBa()
    {
        $params = $this->getParams();
        Misc::scanTiebaByPid($params['baidu_id']);
        return Response::api('刷新成功');
    }

    /**
     * 单个贴吧签到
     *
     * @return JsonResponse
     * @throws BadRequestException
     * @throws BindingResolutionException
     * @throws UnauthorizedException
     */
    public function doSignByTieBaId()
    {
        $params = $this->getParams();
        $user_id = Tools::auth()->id('user');
        $tieba = Tools::model()->TiebaBaiduTieba
            ->where('user_id', $user_id)
            ->where('id', $params['id'])
            ->first();
        return Response::api('签到成功', Misc::doSignByTieBa($tieba)->makeHidden(['baidu']));
    }

    /**
     * 账号所有贴吧签到
     *
     * @return JsonResponse
     * @throws BadRequestException
     * @throws BindingResolutionException
     */
    public function doSignByBaiDuId()
    {
        $params = $this->getParams();
        Misc::doSignByBaiDuId($params['baidu_id']);
        return Response::api('签到成功');
    }

    /**
     * 忽略签到
     *
     * @return JsonResponse
     * @throws BadRequestException
     * @throws BindingResolutionException
     */
    public function noSignTieBa()
    {
        $params = $this->getParams();
        $tieba = Tools::model()->TiebaBaiduTieba->find($params['id']);
        if ($tieba->update(['no' => intval($params['no'])]) === false) throw new BadRequestException('操作失败');
        return Response::api('操作成功', $tieba);
    }

}
