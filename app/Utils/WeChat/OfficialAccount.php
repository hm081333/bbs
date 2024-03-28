<?php

namespace App\Utils\WeChat;

use App\Exceptions\Request\BadRequestException;
use App\Exceptions\Server\InternalServerErrorException;
use App\Models\User\User;
use App\Utils\TieBa\Misc;
use App\Utils\Tools;
use EasyWeChat\Kernel\HttpClient\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class OfficialAccount
{
    /**
     * oauth授权登录链接
     *
     * @param string $callback_url
     *
     * @return string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public static function oauthUrl(string $callback_url)
    {
        $app = \EasyWeChat::officialAccount();
        $oauth = $app->getOauth();
        return $oauth->redirect($callback_url);
    }

    /**
     * oauth登录回调
     *
     * @param string $code
     *
     * @return \App\Models\BaseModel|\App\Models\WeChat\WechatOfficialAccountUser
     * @throws InternalServerErrorException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public static function oauthCallback(string $code)
    {
        $app = \EasyWeChat::officialAccount();
        $oauth = $app->getOauth();
        $user = $oauth->userFromCode($code);
        // https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx10cfa95954e03f6b&redirect_uri=https%3A%2F%2Fbbs2.lyiho.tk&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect
        $open_id = $user->getId();// 对应微信的 openid
        $nickname = $user->getNickname();// 对应微信的 nickname
        // $user->getName();// 对应微信的 nickname
        $avatar = $user->getAvatar();// 头像地址
        // $user->getRaw();// 原始 API 返回的结果
        $wechat_official_account_user = Tools::model()->WeChatWechatOfficialAccountUser->where('open_id', $open_id)->firstOrNew();
        if (!$wechat_official_account_user->exists) {
            $wechat_official_account_user->open_id = $open_id;
            $wechat_official_account_user->nickname = $nickname;
        }
        if (!empty($avatar) && $wechat_official_account_user->headimgurl != $avatar) {
            $wechat_official_account_user->headimgurl = $avatar;
            $file = Tools::file($wechat_official_account_user->headimgurl)->save('wechat/official_account/avatar');
            $wechat_official_account_user->avatar = $file->path;
        }
        if ($wechat_official_account_user->isDirty()) $wechat_official_account_user->save();
        return $wechat_official_account_user;
    }

    /**
     * 发送模版消息
     *
     * @param array $data
     *
     * @return Response|ResponseInterface
     * @throws TransportExceptionInterface
     */
    public static function sendTemplateMessage(array $data): ResponseInterface|Response
    {
        $app = \EasyWeChat::officialAccount();
        $api = $app->getClient();
        return $api->postJson('/cgi-bin/message/template/send', $data);
    }

    /**
     * 获取用户信息
     *
     * @param string $openid
     *
     * @return Response|ResponseInterface
     * @throws TransportExceptionInterface
     */
    public static function userInfo(string $openid)
    {
        $app = \EasyWeChat::officialAccount();
        $api = $app->getClient();
        return $api->get('/cgi-bin/user/info', [
            'openid' => trim($openid),
            // 'lang' => 'zh_CN',
        ]);
    }
}
