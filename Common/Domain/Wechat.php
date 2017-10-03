<?php

class Domain_Wechat
{
	private $appId;
	private $appSecret;

	public function __construct()
	{
		$config = DI()->config->get('app.Wechat')['config'];
		$this->appId = $config['appID'];
		$this->appSecret = $config['appsecret'];
	}

	private function getAccessToken()
	{
		$access_token = DI()->cache->get('access_token');
		if (!isset($access_token) || empty($access_token)) {
			// 如果是企业号用以下URL获取access_token
			// $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
			$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $this->appId . '&secret=' . $this->appSecret;
			$result = DI()->curl->json_get($url);
			if (empty($result)) {
				throw new PhalApi_Exception(T('获取access_token失败'));
			} else {
				$access_token = $result['access_token'];
				$expires_in = $result['expires_in'];
				DI()->cache->set('access_token', $access_token, $expires_in);
				return $access_token;
			}
		} else {
			return $access_token;
		}
	}

	/**
	 * jsapi_ticket是公众号用于调用微信JS接口的临时票据
	 * jsapi_ticket的有效期为7200秒
	 * 通过access_token来获取
	 * @return mixed
	 */
	private function getJsApiTicket()
	{
		$jsapi_ticket = DI()->cache->get('jsapi_ticket');
		if (!isset($jsapi_ticket) || empty($jsapi_ticket)) {
			$accessToken = $this->getAccessToken();
			// 如果是企业号用以下 URL 获取 ticket
			// $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
			$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
			$result = DI()->curl->json_get($url);
			if (empty($result) || $result['errcode'] != 0) {
				throw new PhalApi_Exception(T('获取jsapi_ticket失败'));
			} else {
				$jsapi_ticket = $result['ticket'];
				$expires_in = $result['expires_in'];
				DI()->cache->set('jsapi_ticket', $jsapi_ticket, $expires_in);
				return $jsapi_ticket;
			}
		} else {
			return $jsapi_ticket;
		}
	}

	/**
	 * 拉取身份信息的唯一code
	 * @param string $scope
	 */
	public function getOpenIdCode($scope = 'snsapi_base')
	{
		if (DI()->tool->is_weixin()) {
			//$scope = 'snsapi_userinfo';
			//若提示“该链接无法访问”，请检查参数是否填写错误，是否拥有scope参数对应的授权作用域权限。
			$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $this->appId . '&redirect_uri=' . urlencode(URL . 'tieba.php') . '&response_type=code&scope=' . $scope . '&state=STATE#wechat_redirect';
			Header("Location: $url");
			die;
		}
	}

	/**
	 * 通过code拉取openid和access_token
	 * @param $code
	 * @return mixed
	 * @throws PhalApi_Exception
	 */
	public function getOpenId($code)
	{
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $this->appId . '&secret=' . $this->appSecret . '&code=' . $code . '&grant_type=authorization_code';
		$result = DI()->curl->json_get($url);
		if (!empty($result)) {
			if (isset($result['errmsg'])) {
				throw new PhalApi_Exception(T($result['errmsg']));
			}
			return $result;
		} else {
			throw new PhalApi_Exception_Error(T('失败'));
		}
	}

	/**
	 * $scope = 'snsapi_userinfo'的后续
	 * @param $access_token
	 * @param $open_id
	 * @return mixed
	 * @throws PhalApi_Exception
	 */
	public function getSnsApiUserInfo($code)
	{
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $this->appId . '&secret=' . $this->appSecret . '&code=' . $code . '&grant_type=authorization_code';
		$result = DI()->curl->json_get($url);
		if (!empty($result)) {
			if (isset($result['errmsg'])) {
				throw new PhalApi_Exception(T($result['errmsg']));
			}
			$url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $result['access_token'] . '&openid=' . $result['open_id'] . '&lang=zh_CN ';
			$result = DI()->curl->json_get($url);
			if (!empty($result)) {
				if (isset($result['errmsg'])) {
					throw new PhalApi_Exception(T($result['errmsg']));
				}
				return $result;
			} else {
				throw new PhalApi_Exception_Error(T('失败'));
			}
		} else {
			throw new PhalApi_Exception_Error(T('失败'));
		}
	}

	/**
	 * 通过获取的openid达到自动登陆的效果
	 * @param $code
	 * @throws PhalApi_Exception
	 * @throws PhalApi_Exception_Error
	 */
	public function openIdLogin($code)
	{
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $this->appId . '&secret=' . $this->appSecret . '&code=' . $code . '&grant_type=authorization_code';
		$result = DI()->curl->json_get($url);
		if (!empty($result)) {
			if (isset($result['errmsg'])) {
				throw new PhalApi_Exception(T($result['errmsg']));
			}
			$open_id = $result['openid'];
			$user_model = new Model_User();
			$user = $user_model->getInfo(array('open_id' => $open_id));
			if ($user) {
				//将用户名存如SESSION中
				$_SESSION['user_id'] = $user['id'];
				$_SESSION['user_name'] = $user['user_name'];
				$_SESSION['user_auth'] = $user['auth'];
			}
		} else {
			throw new PhalApi_Exception_Error(T('失败'));
		}
	}

	/**
	 * 生成jsSDK权限验证配置
	 * @return array
	 */
	public function getSignPackage()
	{
		$jsapiTicket = $this->getJsApiTicket();
		// 注意 URL 一定要动态获取，不能 hardcode.
		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		$url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$timestamp = NOW_TIME;
		$nonceStr = DI()->tool->createRandStr(16);
		// 这里参数的顺序要按照 key 值 ASCII 码升序排序
		$string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
		$signature = sha1($string);
		$signPackage = array(
			"appId" => $this->appId,
			"nonceStr" => $nonceStr,
			"timestamp" => $timestamp,
			"url" => $url,
			"signature" => $signature,
			"rawString" => $string
		);
		return $signPackage;
	}


}
