<?php

class Domain_Tieba
{

	/**
	 * 删除贴吧
	 * @param $tieba_id
	 * @return bool
	 * @throws PhalApi_Exception
	 */
	public static function deleteTieba($tieba_id)
	{
		$tieba_model = new Model_Tieba();
		$result = $tieba_model->delete($tieba_id);
		if ($result === false) {
			throw new PhalApi_Exception(T('删除失败'));
		}
		return true;
	}

	/**
	 * 忽略签到
	 * @param $tieba_id
	 * @param $no
	 * @return bool
	 * @throws PhalApi_Exception
	 */
	public static function noSignTieba($tieba_id, $no)
	{
		$tieba_model = new Model_Tieba();
		$result = $tieba_model->update($tieba_id, array('no' => $no));
		if ($result === false) {
			throw new PhalApi_Exception(T('操作失败'));
		}
		return true;
	}

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
	 * 扫描指定用户的所有贴吧并储存--用于一键刷新
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
		set_time_limit(0); // 不超时
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
	 * 得到贴吧 FID
	 * @param string $kw 贴吧名
	 * @return string FID
	 */
	public static function getFid($kw)
	{
		$url = 'http://tieba.baidu.com/mo/m?kw=' . urlencode($kw), array('User-Agent: fuck phone', 'Referer: http://wapp.baidu.com/', 'Content-Type: application/x-www-form-urlencoded', 'Cookie:BAIDUID=' . strtoupper(md5(time())));
		$s = DI()->curl->get($url);
		$x = DI()->tool->easy_match('<input type="hidden" name="fid" value="*"/>', $s);
		if (isset($x[1])) {
			return $x[1];
		} else {
			return false;
		}
	}

	/**
	 * 通过UserID获得指定用户的贴吧数据表
	 * @param string $uid UserID
	 */
	/*public static function getTable($user_id)
	{
		$user_model = new Model_User();
		$x = $user_model->get($user_id);
		return $x['t'];
	}*/

	/**
	 * 执行全部签到任务
	 * @param string $table 表
	 */
	public static function doSign($table)
	{
		$day_time = DateHelper::getDayTime(); // 今天开始的时间和结束的时间的时间戳

		//处理所有未签到的贴吧
		$q = array();
		$tieba_model = new Model_Tieba();
		$where = array();
		$where['no = ?'] = 0;
		$where['latest < ?'] = $day_time['begin'];
		$qs = $tieba_model->getListByWhere($where);
		foreach ($qs as $index => $qss) {
			$q[] = array(
				'id' => $qss['id'],
				'user_id' => $qss['user_id'],
				'baidu_id' => $qss['baidu_id'],
				'fid' => $qss['fid'],
				'tieba' => $qss['tieba'],
				'no' => $qss['no'],
				'status' => $qss['status'],
				'latest' => $qss['latest'],
				'last_error' => $qss['last_error']
			);
		}
		shuffle($q);

		foreach ($q as $x) {
			self::DoSign_All($x['user_id'], $x['tieba'], $x['id'], $sign_mode, $x['baidu_id'], $x['fid']);
		}
	}

	/**
	 * 对一个贴吧执行完整的签到任务
	 */
	public static function DoSign_All($user_id, $kw, $id, $sign_mode, $baidu_id, $fid)
	{
		$again_error_id = 160002; //重复签到错误代码
		$again_error_id_2 = 1101; //特殊的重复签到错误代码！！！签到过快=已签到
		$again_error_id_3 = 1102; //特殊的重复签到错误代码！！！签到过快=已签到
		$status_succ = false;

		$baiduid_model = new Model_BaiduId();
		$bdid = $baiduid_model->get($baidu_id, 'bduss');
		$ck = $bdid['bduss'];
		$kw = addslashes($kw);

		if (empty($fid)) {
			$fid = self::getFid($kw);
			$tieba_model = new Model_Tieba();
			$tieba_model->update($id, array('fid' => $fid));
		}

		if (!empty($sign_mode) && in_array('1', $sign_mode) && $status_succ === false) {
			$r = self::DoSign_Client($user_id, $kw, $id, $baidu_id, $fid, $ck);
			$v = json_decode($r, true);
			if ($v != $r && $v != NULL) {//decode失败时会直接返回原文或NULL
				if (empty($v['error_code']) || $v['error_code'] == $again_error_id) {
					$status_succ = true;
				} else {
					$error_code = $v['error_code'];
					$error_msg = $v['error_msg'];
				}
			}
		}

		if (!empty($sign_mode) && in_array('3', $sign_mode) && $status_succ === false) {
			$r = self::DoSign_Mobile($user_id, $kw, $id, $baidu_id, $fid, $ck);
			$v = json_decode($r, true);
			if ($v != $r && $v != NULL) {//decode失败时会直接返回原文或NULL
				if (empty($v['no']) || $v['no'] == $again_error_id_2 || $v['no'] == $again_error_id_3) {
					$status_succ = true;
				} else {
					$error_code = $v['no'];
					$error_msg = $v['error'];
				}
			}
		}

		if (!empty($sign_mode) && in_array('2', $sign_mode) && $status_succ === false) {
			if (self::DoSign_Default($user_id, $kw, $id, $baidu_id, $fid, $ck) === true) {
				$status_succ = true;
			}
		}

		if ($status_succ === true) {
			$tieba_model->update($id, array('latest' => NOW_TIME, 'status' => 0, 'last_error' => ''));
		} else {
			$tieba_model->update($id, array('latest' => NOW_TIME, 'status' => $error_code, 'last_error' => $error_msg));
		}

		//usleep(option::get('sign_sleep') * 1000);
	}

	/**
	 * 客户端签到
	 */
	public static function DoSign_Client($uid, $kw, $id, $pid, $fid, $ck)
	{
		var_dump($uid, $kw, $id, $pid, $fid, $ck);
		exit;
		$ch = new wcurl('http://c.tieba.baidu.com/c/c/forum/sign', array('Content-Type: application/x-www-form-urlencoded', 'User-Agent: Fucking iPhone/1.0 BadApple/99.1'));
		$ch->addcookie("BDUSS=" . $ck);
		$temp = array(
			'BDUSS' => misc::getCookie($pid),
			'_client_id' => '03-00-DA-59-05-00-72-96-06-00-01-00-04-00-4C-43-01-00-34-F4-02-00-BC-25-09-00-4E-36',
			'_client_type' => '4',
			'_client_version' => '1.2.1.17',
			'_phone_imei' => '540b43b59d21b7a4824e1fd31b08e9a6',
			'fid' => $fid,
			'kw' => $kw,
			'net_type' => '3',
			'tbs' => misc::getTbs($uid, $ck)
		);
		$x = '';
		foreach ($temp as $k => $v) {
			// var_dump($k);
			$x .= $k . '=' . $v;
		}
		$temp['sign'] = strtoupper(md5($x . 'tiebaclient!!!'));
		return $ch->post($temp);
	}

}
