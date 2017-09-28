<?php

class Domain_Tieba
{

	public static function addBduss($user_id, $bduss)
	{
		// 去除双引号和bduss=
		$bduss = str_replace('"', '', $bduss);
		$bduss = str_ireplace('BDUSS=', '', $bduss);
		$bduss = str_replace(' ', '', $bduss);
		$bduss = DI()->tool->sqlAdds($bduss);
		$baidu_name = DI()->tool->sqlAdds(self::getBaiduId($bduss));
		if (empty($baidu_name)) {
			//throw new PhalApi_Exception(T('您的 BDUSS Cookie 信息有误，请核验后重新绑定'));
			return T('您的 BDUSS Cookie 信息有误，请核验后重新绑定');
		}
		//doAction('baiduid_set_2');
		$baiduid_model = new Model_BaiduId();
		$insert_rs = $baiduid_model->insert(array('user_id' => $user_id, 'bduss' => $bduss, 'name' => $baidu_name));
		if ($insert_rs === false) {
			return T('插入表失败');
		}
		return array('msg' => T('添加新Bduss成功'));
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
		$cma = $baiduid_model->get($pid);
		$tieba_model = new Model_Tieba();
		//$tb_sl = $tieba_model->count(array('user_id' => $user_id));
		//$ptb_sl = $tieba_model->count(array('user_id' => $user_id, 'baidu_id' => $pid));
		$user_id = $cma['user_id'];
		$bduss = $cma['bduss'];
		$bname = $cma['name'];
		$pid = $cma['id'];
		unset($cma);
		$bid = self::getUserid($bname);
		$pn = 1;
		$a = 0;
		while (true) {
			if (empty($bid)) break;
			$rc = self::getTieba($bid, $bduss, $pn);
			$ngf = $rc['forum_list']['non-gconforum'];
			if (!empty($rc['forum_list']['gconforum'])) {
				foreach ($rc['forum_list']['gconforum'] as $v) {
					$ngf[] = $v;
				}
			}
			if (!empty($ngf) && is_array($ngf)) {
				$refresh_time = $rc['time'];
				$baiduid_model->update($pid, array('refresh_time' => $refresh_time));
				foreach ($ngf as $v) {
					$vn = addslashes(htmlspecialchars($v['name']));
					$ist = $tieba_model->count(array('baidu_id' => $pid, 'tieba' => $vn));
					if ($ist == 0) {
						$a++;
						$tieba_model->insert(array('baidu_id' => $pid, 'fid' => $v['id'], 'user_id' => $user_id, 'tieba' => $vn, 'refresh_time' => $refresh_time));
					}
				}
			}
			if ((count($ngf) < 1)) {
				break;
			}
			$pn++;
		}
	}

	/**
	 * 获取贴吧用户id
	 * 获取指定pid用户userid--根据贴吧用户名找
	 */
	public static function getUserid($name)
	{
		$url = "http://tieba.baidu.com/home/get/panel?ie=utf-8&un={$name}";
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

}
