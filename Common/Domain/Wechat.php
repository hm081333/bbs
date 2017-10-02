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

	public function getAccessToken()
	{
		$access_token = DI()->cache->get('access_token');
		if (empty($access_token)) {
			$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $this->appId . '&secret=' . $this->appSecret;
			$result = DI()->curl->json_get($url);
			if (empty($result)) {
				throw new PhalApi_Exception(T('获取access_token失败'));
			} else {
				$access_token = $result['access_token'];
				DI()->cache->set('access_token', $result['access_token'], $result['expires_in']);
				return $access_token;
			}
		} else {
			return $access_token;
		}
	}

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

	public function getOpenId($code)
	{
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $this->appId . '&secret=' . $this->appSecret . '&code=' . $code . '&grant_type=authorization_code';
		$result = DI()->curl->json_get($url);
		if (!empty($result)) {
			$open_id = $result['openid'];
			$user_model = new Model_User();
			$user = $user_model->getInfo(array('open_id' => $open_id));
			if ($user) {
				//将用户名存如SESSION中
				$_SESSION['user_id'] = $user['id'];
				$_SESSION['user_name'] = $user['user_name'];
				$_SESSION['user_auth'] = $user['auth'];
			}
		}
	}


}
