<?php

/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2017/6/23
 * Time: 下午 11:49
 */
class Common_Function
{


	/**
	 * @param $question 问题
	 * @return array
	 */
	public static function tuling($question)
	{
		$tuling_config = DI()->config->get('app.tuling_config');
		$rs = DI()->curl->json_post($tuling_config['URI'], array('key' => $tuling_config['APIkey'], 'info' => $question));
		return $rs;
	}

	public static function baidu_map($data, $data_type = 'location')
	{
		$baidu_map_config = DI()->config->get('app.baidu_map_config');
		//API控制台申请得到的ak（此处ak值仅供验证参考使用）
		$ak = $baidu_map_config['ak'];
		//应用类型为for server, 请求校验方式为sn校验方式时，系统会自动生成sk，可以在应用配置-设置中选择Security Key显示进行查看（此处sk值仅供验证参考使用）
		$sk = $baidu_map_config['sk'];
		//以Geocoding服务为例，地理编码的请求url，参数待填
		if ($data_type == 'address') {
			$url = "http://api.map.baidu.com/geocoder/v2/?address=%s&output=%s&ak=%s&sn=%s";
		} elseif ($data_type == 'location') {
			$url = 'http://api.map.baidu.com/geocoder/v2/?location=%s&output=%s&ak=%s&sn=%s';
		}
		//get请求uri前缀
		$uri = '/geocoder/v2/';
		//地理编码的请求output参数
		$output = 'json';
		//构造请求串数组
		$param = array(
			$data_type => $data,
			'output' => $output,
			'ak' => $ak
		);
		//调用sn计算函数，默认get请求
		$sn = self::caculateAKSN($ak, $sk, $uri, $param);
		//请求参数中有中文、特殊字符等需要进行urlencode，确保请求串与sn对应
		$request = sprintf($url, urlencode($data), $output, $ak, $sn);
		//执行请求的url
		$rs = DI()->curl->json_get($request);
		if ($rs['status'] !== 0) {
			DI()->logger->error('百毒地图API报错，错误码：' . $rs['status'] . '，错误信息：' . $rs['message']);
		}
		return $rs;
	}

	public static function caculateAKSN($ak, $sk, $url, $param, $method = 'GET')
	{
		if ($method === 'POST') {
			ksort($param);
		}
		$querystring = http_build_query($param);
		return md5(urlencode($url . '?' . $querystring . $sk));
	}

	public static function getImage($url = '', $fileName = '')
	{
		$fileName = API_ROOT . '/Public/static/upload/wechat/' . $fileName . '.jpg';
		$ch = curl_init();
		$fp = fopen($fileName, 'wb');
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_exec($ch);
		curl_close($ch);
		fclose($fp);
	}

	/**
	 * @param $code string 快递公司代码
	 * @param $sn string 快递单号
	 */
	public static function getLogistics($code, $sn)
	{
		return DI()->curl->json_get('http://www.kuaidi100.com/query?type=' . $code . '&postid=' . $sn);
	}

	public static function newBduss($user_id, $bduss)
	{
		// 去除双引号和bduss=
		$bduss = str_replace('"', '', $bduss);
		$bduss = str_ireplace('BDUSS=', '', $bduss);
		$bduss = str_replace(' ', '', $bduss);
		$bduss = DI()->tool->sqlAdds($bduss);
		$baidu_name = DI()->tool->sqlAdds(self::getBaiduId($bduss));
		if (empty($baidu_name)) {
			throw new PhalApi_Exception(T('您的 BDUSS Cookie 信息有误，请核验后重新绑定'));
		}
		//doAction('baiduid_set_2');
		$baiduid_model = new Model_BaiduId();
		$baiduid_model->insert(array('user_id' => $user_id, 'bduss' => $bduss, 'name' => $baidu_name));
	}

	/**
	 * 获取一个bduss对应的百度用户名
	 * @param string $bduss BDUSS
	 * @return string|bool 百度用户名，失败返回FALSE
	 */
	public static function getBaiduId($bduss)
	{
		//$c = new wcurl('http://wapp.baidu.com/');
		//$c->addCookie(array('BDUSS' => $bduss, 'BAIDUID' => strtoupper(md5(time()))));
		//$data = $c->get();
		//$c->close();
		$url = 'http://wapp.baidu.com/';
		DI()->curl->setCookie(array('BDUSS' => $bduss, 'BAIDUID' => strtoupper(md5(time()))));
		$data = DI()->curl->get($url);
		return urldecode(DI()->tool->textMiddle($data, 'i?un=', '">'));
	}

	/**
	 * 扫描指定用户的所有贴吧并储存
	 * @param UserID，如果留空，表示当前用户的UID
	 */
	public static function scanTiebaByUser($user_id = '')
	{
		//global $i;
		$baiduid_model = new Model_BaiduId();
		set_time_limit(0);
		//if (empty($user_id)) {
		//	$bduss = $i['user']['bduss'];
		//} else {
		$bx = $baiduid_model->getListByWhere(array('user_id' => $user_id));
		foreach ($bx as $by) {
			$upid = $by['id'];
			$bduss[$upid] = $by['bduss'];
		}
		//}
		foreach ($bduss as $pid => $ubduss) {
			$t = self::scanTiebaByPid($pid);
		}
	}

	/**
	 * 扫描指定PID的所有贴吧
	 * @param string $pid PID
	 */
	public static function scanTiebaByPid($pid)
	{
		$baiduid_model = new Model_BaiduId();
		//baidu_id
		$cma = $baiduid_model->get($pid);
		$user_id = $cma['user_id'];
		$tieba_model = new Model_Tieba();
		$tb_sl = $tieba_model->count(array('user_id' => $user_id));
		$bduss = $cma['bduss'];
		$pid = $cma['id'];
		$bid = self::getUserid($pid);
		$pn = 1;
		$a = 0;
		while (true) {
			if (empty($bid)) break;
			$rc = self::getTieba($bid, $bduss, $pn);
			$ngf = $rc['forum_list']['non-gconforum'];
			foreach ($rc['forum_list']['gconforum'] as $v) {
				$ngf[] = $v;
			}
			foreach ($ngf as $v) {
				$vn = addslashes(htmlspecialchars($v['name']));
				$ist = $tieba_model->count(array('baidu_id' => $pid, 'tieba' => $vn));
				if ($ist['c'] == 0) {
					$a++;
					$tieba_model->insert(array('baidu_id' => $pid, 'fid' => $v['id'], 'user_id' => $user_id, 'tieba' => $vn));
				}
			}
			if ((count($ngf) < 1)) break;
			$pn++;
		}
	}

	/**
	 * 通过UserID获得指定用户的贴吧数据表
	 * @param string $uid UserID
	 */
	public static function getTable($user_id)
	{
		$user_model = new Model_User();
		$x = $user_model->get($user_id);
		return $x['t'];
	}

	/**
	 * 获取贴吧用户id
	 * 获取指定pid用户userid
	 */
	public static function getUserid($pid)
	{
		$baiduid_model = new Model_BaiduId();
		$ub = $baiduid_model->get($pid);
		$url = "http://tieba.baidu.com/home/get/panel?ie=utf-8&un={$ub['name']}";
		$ur = DI()->curl->json_get($url);
		$userid = $ur['data']['id'];
		return $userid;
	}

	/**
	 * 获取指定pid
	 */
	public static function getTieba($userid, $bduss, $pn)
	{
		$head = array();
		$head[] = 'Content-Type: application/x-www-form-urlencoded';
		$head[] = 'User-Agent: Mozilla/5.0 (SymbianOS/9.3; Series60/3.2 NokiaE72-1/021.021; Profile/MIDP-2.1 Configuration/CLDC-1.1 ) AppleWebKit/525 (KHTML, like Gecko) Version/3.0 BrowserNG/7.1.16352';
		//$tl = new wcurl('http://c.tieba.baidu.com/c/f/forum/like', $head);
		$url = 'http://c.tieba.baidu.com/c/f/forum/like';
		$data = array(
			'_client_id' => 'wappc_' . time() . '_' . '258',
			'_client_type' => 2,
			'_client_version' => '6.5.8',
			'_phone_imei' => '357143042411618',
			'from' => 'baidu_appstore',
			'is_guest' => 1,
			'model' => 'H60-L01',
			'page_no' => $pn,
			'page_size' => 200,
			'timestamp' => time() . '903',
			'uid' => $userid,
		);
		$sign_str = '';
		foreach ($data as $k => $v) {
			$sign_str .= $k . '=' . $v;
		}
		$sign = strtoupper(md5($sign_str . 'tiebaclient!!!'));
		$data['sign'] = $sign;
		DI()->curl->setHeader($head);
		DI()->curl->setCookie(array('BDUSS' => $bduss));
		DI()->curl->setOption(array(CURLOPT_SSL_VERIFYPEER => FALSE, CURLOPT_FOLLOWLOCATION => TRUE));
		$rt = DI()->curl->json_post($url, $data);
		return $rt;
	}


}