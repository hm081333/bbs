<?php

class Domain_Common
{

	public static function checkEmail($email)
	{
		$check = "/^[0-9a-zA-Z_-]+@[0-9a-zA-Z_-]+(\.[0-9a-zA-Z_-]+){0,3}$/";

		if (preg_match($check, $email)) {
			return true;
		} else {
			return false;
		}
	}

	public static function hash($password, $algo = PASSWORD_DEFAULT, $options = null)
	{
		if (empty($options)) {
			return password_hash($password, $algo);
		} else {
			return password_hash($password, $algo, $options);
		}
	}

	public static function verify($password, $hash)
	{
		return password_verify($password, $hash);
	}

	/**
	 * @return string
	 */
	public static function create_Google_Auth()
	{
		$ga = new GoogleAuthenticator_Lite();
		return $secret = $ga->createSecret();
	}

	/**
	 * @param $secret
	 * @param string $name 标识
	 * @return string
	 */
	public static function get_Google_Auth_Url($secret, $name = 'LYi-Ho')
	{
		$ga = new GoogleAuthenticator_Lite();
		return $qrCodeUrl = $ga->getQRCodeGoogleUrl($name, $secret);
	}

	/**
	 * @param $secret
	 * @return string
	 */
	public static function get_Google_Auth_Code($secret)
	{
		$ga = new GoogleAuthenticator_Lite();
		return $oneCode = $ga->getCode($secret);
	}

	/**
	 * @param $secret 服务端的 "安全密匙SecretKey"
	 * @param $oneCode 手机上看到的 "一次性验证码"
	 * @param $discrepancy 容差时间,这里是2 那么就是 2* 30 sec 一分钟.
	 * @return bool
	 */
	public static function verify_Google_Auth_Code($secret, $oneCode, $discrepancy = 1)
	{
		$ga = new GoogleAuthenticator_Lite();
		$checkResult = $ga->verifyCode($secret, $oneCode, $discrepancy);
		if ($checkResult) {
			return true;
		} else {
			return false;
		}
	}

	public static function send_smsbao($phone, $content)
	{
		/*$statusStr = array(
			"0" => "短信发送成功",
			"-1" => "参数不全",
			"-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
			"30" => "密码错误",
			"40" => "账号不存在",
			"41" => "余额不足",
			"42" => "帐户已过期",
			"43" => "IP地址限制",
			"50" => "内容含有敏感词",
			"51" => "手机号码不正确"
		);*/
		$statusStr = DI()->config->get('return_str.smsbao');
		$smsapi = "http://api.smsbao.com/";
		$smsbao = DI()->config->get('sms.smsbao');
		$user = $smsbao['username']; //短信平台帐号
		$pass = md5($smsbao['password']); //短信平台密码
		//$content="短信内容";//要发送的短信内容
		//$phone = "*****";//要发送短信的手机号码
		$sendurl = $smsapi . "sms?u=" . $user . "&p=" . $pass . "&m=" . $phone . "&c=" . urlencode($content);
		$result = file_get_contents($sendurl);
		if ($result == 0) {
			return true;
		} else {
			return $statusStr[$result];
		}
	}

	/**
	 * 查询短信宝剩余短信数量
	 * @param string $username
	 * @param string $password
	 * @return array
	 */
	public static function query_smsbao($username = '', $password = '')
	{
		$statusStr = DI()->config->get('return_str.smsbao');
		if (empty($username) || empty($password)) {
			$smsbao = DI()->config->get('sms.smsbao');
		} else {
			$smsbao['username'] = $username;
			$smsbao['password'] = $password;
		}
		$smsapi = "http://api.smsbao.com/";
		$user = $smsbao['username']; //短信平台帐号
		$pass = md5($smsbao['password']); //短信平台密码
		$sendurl = $smsapi . "query?u=" . $user . "&p=" . $pass;
		$result = file_get_contents($sendurl);
		$result = self::trimall($result);
		$result = explode(',', $result); // 拆分成数组

		//合并有用数值成为数组
		$rs = array();
		$rs[0] = $result[0]; // 查询返回结果
		$rs['msg'] = $statusStr[$result[0]];
		if ($rs[0] == 0) {
			$rs[1] = $result[1]; // 发送条数
			$rs[2] = $result[2]; // 剩余条数
		}
		//var_dump($rs);
		return $rs;
	}

	public static function trimall($str) // 替换换行
	{
		$qian = array(" ", "　", "\t", "\n", "\r");
		$hou = array(",", ",", ",", ",", ",");
		return str_replace($qian, $hou, $str);
	}

}
