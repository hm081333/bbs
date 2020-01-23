<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2018-11-26
 * Time: 11:14:57
 */

namespace Common\Domain;

/**
 * 微信公众平台 领域层
 * Class WeChatMediaPlatform
 * @package Common\Domain
 * @author  LYi-Ho 2018-11-26 11:14:57
 */
class WeChatPublicPlatform
{
    use Common;
    private $appId;
    private $appSecret;

    public function __construct()
    {
        $config = $this->settingDomain()::getSetting('wechat');
        $this->appId = $config['app_id'] ?? '';
        $this->appSecret = $config['app_secret'] ?? '';
    }

    /**
     * @return \Common\Domain\Setting
     */
    private function settingDomain()
    {
        return self::getDomain('Setting');
    }

    /**
     * 拉取身份信息的唯一code
     * @param string $scope
     */
    public function getOpenIdCode($scope = 'snsapi_base')
    {
        if (\Common\isWeChat()) {
            //$scope = 'snsapi_userinfo';
            //若提示“该链接无法访问”，请检查参数是否填写错误，是否拥有scope参数对应的授权作用域权限。
            $redirect_uri = urlencode(URL_ROOT . 'tieba.php');
            $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$this->appId} &redirect_uri={$redirect_uri}&response_type=code&scope={$scope}&state=STATE#wechat_redirect";
            Header("Location: $url");
            die;
        }
    }

    /**
     * 通过code拉取openid和access_token
     * @param $code
     * @return mixed
     * @throws \Library\Exception\Exception
     */
    public function getOpenId($code)
    {
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->appId}&secret={$this->appSecret}&code={$code}&grant_type=authorization_code";
        $result = self::DI()->curl->get($url);
        $result = json_decode($result, true);
        if (!empty($result)) {
            if (isset($result['errmsg'])) {
                throw new \Library\Exception\Exception(\PhalApi\T($result['errmsg']));
            }
            return $result;
        } else {
            throw new \Library\Exception\Exception(\PhalApi\T('失败'));
        }
    }

    /**
     * $scope = 'snsapi_userinfo'的后续
     * @param $code
     * @return mixed
     * @throws \Library\Exception\Exception
     */
    public function getSnsApiUserInfo($code)
    {
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->appId}&secret={$this->appSecret}&code={$code}&grant_type=authorization_code";
        $result = self::DI()->curl->get($url);
        $result = json_decode($result, true);
        if (!empty($result)) {
            if (isset($result['errmsg'])) {
                throw new \Library\Exception\Exception(\PhalApi\T($result['errmsg']));
            }
            $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$result['access_token']}&openid={$result['open_id']}&lang=zh_CN";
            $result = self::DI()->curl->get($url);
            $result = json_decode($result, true);
            if (!empty($result)) {
                if (isset($result['errmsg'])) {
                    throw new \Library\Exception\Exception(\PhalApi\T($result['errmsg']));
                }
                return $result;
            } else {
                throw new \Library\Exception\Exception(\PhalApi\T('失败'));
            }
        } else {
            throw new \Library\Exception\Exception(\PhalApi\T('失败'));
        }
    }

    /**
     * 通过获取的openid达到自动登陆的效果
     * @param $code
     * @throws \Library\Exception\Exception
     */
    public function openIdLogin($code)
    {
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->appId}&secret={$this->appSecret}&code={$code}&grant_type=authorization_code";
        $result = self::DI()->curl->get($url);
        $result = json_decode($result, true);
        if (!empty($result)) {
            if (isset($result['errmsg'])) {
                throw new \Library\Exception\Exception(\PhalApi\T($result['errmsg']));
            }
            $open_id = $result['openid'];
            $user_model = self::getModel('User');
            $user = $user_model->getInfo(['open_id' => $open_id]);
            if ($user) {
                //将用户名存如SESSION中
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['user_name'];
                $_SESSION['user_auth'] = $user['auth'];
            }
        } else {
            throw new \Library\Exception\Exception(\PhalApi\T('失败'));
        }
    }

    /**
     * 发送贴吧签到详情
     * @param bool $openid
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \Library\Exception\BadRequestException
     * @throws \Library\Exception\InternalServerErrorException
     */
    private function sendTiebaSignDetail($openid = false)
    {
        if (empty($openid)) {
            throw new \Library\Exception\BadRequestException(\PhalApi\T('缺少openid'));
        }

        $info = self::getDomain('TieBa')::getSignStatus($openid);
        if ($info == false) {
            throw new \Library\Exception\InternalServerErrorException(\PhalApi\T('获取状态失败'));
        }

        $result = \Common\DI()->wechat->template_message->send([
            'touser' => $openid,
            'template_id' => 'Ogvc_rROWerSHvfgo1IOJIL103bso0H3jLYEAwTuKKg',
            'url' => 'http://bbs2.lyihe2.tk/tieba',
            // 'miniprogram' => [
            //     'appid' => 'xxxxxxx',
            //     'pagepath' => 'pages/xxx',
            // ],
            'data' => [
                'user_name' => [
                    'value' => $info['user_name'],
                    'color' => '#173177',
                ],
                'greeting' => [
                    'value' => $info['greeting'],
                    'color' => '#173177',
                ],
                'tieba_count' => [
                    'value' => $info['tieba_count'],
                    'color' => '#173177',
                ],
                'success_count' => [
                    'value' => $info['success_count'],
                    'color' => '#173177',
                ],
                'fail_count' => [
                    'value' => $info['fail_count'],
                    'color' => '#173177',
                ],
                'ignore_count' => [
                    'value' => $info['ignore_count'],
                    'color' => '#173177',
                ],
            ],
        ]);

        self::DI()->logger->debug('微信推送结果', $result);

        return $result;
    }

    /**
     * 公众号发送贴吧签到日志
     */
    public function sendTieBaSignDetailByCron()
    {
        $user_model = self::getModel('User');
        $users = $user_model->getListByWhere(['open_id IS NOT ?' => null, 'sign_notice' => 1], 'open_id');
        foreach ($users as $user) {
            try {
                $this->sendTiebaSignDetail($user['open_id']);
            } catch (\Exception $e) {
                self::DI()->logger->error($e->getMessage());
            }
        }
    }


}
