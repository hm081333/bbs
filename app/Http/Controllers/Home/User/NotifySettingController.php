<?php

namespace App\Http\Controllers\Home\User;

use App\Utils\Tools;
use Illuminate\Support\Facades\Response;

class NotifySettingController extends \App\Http\Controllers\BaseController
{
    protected function getRules()
    {
        return [
            'setBark' => [
                'enable' => ['desc' => '开启推送', 'boolean', 'required'],
                'push_url' => ['desc' => '推送地址', 'url'],
                'server_address' => ['desc' => '推送地址', 'url', 'required_without' => 'push_url'],
                'device_key' => ['desc' => '设备Key', 'string', 'required_without' => 'push_url'],
                'level' => ['desc' => '推送中断级别', 'string', 'in' => ['active', 'timeSensitive', 'passive']],
                'sound' => ['desc' => '推送声音', 'string'],
                'base_group' => ['desc' => '消息分组', 'string'],
            ],
            'setPushPlus' => [
                'enable' => ['desc' => '开启推送', 'boolean', 'required'],
                'token' => ['desc' => 'TOKEN', 'string', 'required'],
                'topic' => ['desc' => '群组编码', 'string'],
            ],
            'setDingDingBot' => [
                'enable' => ['desc' => '开启推送', 'boolean', 'required'],
                'token' => ['desc' => '钉钉机器人TOKEN', 'string', 'required'],
                'secret' => ['desc' => '钉钉机器人秘钥', 'string'],
            ],
        ];
    }

    //region Bark
    public function getBark()
    {
        $user_id = Tools::auth()->id('user');
        $bark_setting = Tools::model()->UserUserNotifyBarkSetting
            ->where('user_id', $user_id)
            ->first();
        return Response::api('', $bark_setting);
    }

    /**
     * Bark推送设置
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\Request\BadRequestException
     * @throws \App\Exceptions\Request\UnauthorizedException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function setBark()
    {
        $user_id = Tools::auth()->id('user');
        $params = $this->getParams();
        if (!empty($params['push_url'])) {
            preg_match('/((https|http|ssftp|rtsp|mms)?:\/\/([^\/]+\/)+)(.*)/', rtrim($params['push_url'], '/'), $matches);
            $params['server_address'] = rtrim($matches[1], '/');
            $params['device_key'] = $matches[4];
            unset($params['push_url'], $matches);
        }
        $bark_setting = Tools::model()->UserUserNotifyBarkSetting
            ->firstOrNew([
                'user_id' => $user_id,
            ]);
        $bark_setting->forceFill($params)->save();
        return Response::api('设置成功', $bark_setting);
    }

    //endregion

    //region Push Plus
    public function getPushPlus()
    {
        $user_id = Tools::auth()->id('user');
        $bark_setting = Tools::model()->UserUserNotifyPushPlusSetting
            ->where('user_id', $user_id)
            ->first();
        return Response::api('', $bark_setting);
    }

    /**
     * Push Plus推送设置
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\Request\BadRequestException
     * @throws \App\Exceptions\Request\UnauthorizedException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function setPushPlus()
    {
        $user_id = Tools::auth()->id('user');
        $params = $this->getParams();
        $setting = Tools::model()->UserUserNotifyPushPlusSetting
            ->firstOrNew([
                'user_id' => $user_id,
            ]);
        $setting->forceFill($params)->save();
        return Response::api('设置成功', $setting);
    }
    //endregion

    //region 钉钉机器人
    public function getDingDingBot()
    {
        $user_id = Tools::auth()->id('user');
        $bark_setting = Tools::model()->UserUserNotifyDingDingBotSetting
            ->where('user_id', $user_id)
            ->first();
        return Response::api('', $bark_setting);
    }

    /**
     * Push Plus推送设置
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\Request\BadRequestException
     * @throws \App\Exceptions\Request\UnauthorizedException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function setDingDingBot()
    {
        $user_id = Tools::auth()->id('user');
        $params = $this->getParams();
        $setting = Tools::model()->UserUserNotifyDingDingBotSetting
            ->firstOrNew([
                'user_id' => $user_id,
            ]);
        $setting->forceFill($params)->save();
        return Response::api('设置成功', $setting);
    }
    //endregion

}
