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
			if (empty($bid)) {
				break;
			}
			$rc = self::getTieba($bid, $bduss, $pn);
			$ngf = isset($rc["forum_list"]["non-gconforum"]) ? $rc["forum_list"]["non-gconforum"] : array();
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
		$url = 'http://tieba.baidu.com/mo/m?kw=' . urlencode($kw);
		DI()->curl->setHeader(array('User-Agent: fuck phone', 'Referer: http://wapp.baidu.com/', 'Content-Type: application/x-www-form-urlencoded', 'Cookie:BAIDUID=' . strtoupper(md5(time()))));
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
	 * 执行全部贴吧用户的签到任务
	 */
	public static function doSignAll()
	{
		set_time_limit(0);
		$day_time = DateHelper::getDayTime(); // 今天开始的时间和结束的时间的时间戳
		//处理所有未签到的贴吧
		$tieba_model = new Model_Tieba();
		$where = array();
		$where['no = ?'] = 0; // 不忽略签到
		$where['latest < ?'] = $day_time['begin']; // 今天没有签到
		$total_sign_tieba = $tieba_model->count($where); // 该条件下所有贴吧数量
		$limit = 100; // 100条100条循环拿
		$count = ceil($total_sign_tieba / $limit); // 循环100条的次数
		$else = 0; // 已遍历的数量
		for ($i = 1; $i <= $count; $i++) {
			$qs = $tieba_model->getList($limit, 0, $where, '*', 'id asc');
			$else += $qs['total'];
			$q = array();
			foreach ($qs['rows'] as $index => $qss) {
				$q[] = array(
					'id' => $qss['id'],
					//'user_id' => $qss['user_id'],
					'baidu_id' => $qss['baidu_id'],
					'fid' => $qss['fid'],
					'tieba' => $qss['tieba'],
					//'no' => $qss['no'],
					//'status' => $qss['status'],
					//'latest' => $qss['latest'],
					//'last_error' => $qss['last_error']
				);
			}
			shuffle($q);
			foreach ($q as $x) {
				self::doSign($x['tieba'], $x['id'], $x['baidu_id'], $x['fid']);
			}
		}
	}

	/**
	 * 执行一个贴吧用户的签到
	 */
	public static function doSignByBaiduId($baidu_id)
	{
		set_time_limit(0);
		$day_time = DateHelper::getDayTime(); // 今天开始的时间和结束的时间的时间戳
		//处理所有未签到的贴吧
		$tieba_model = new Model_Tieba();
		$where = array();
		$where['baidu_id = ?'] = $baidu_id; // 该贴吧用户
		$where['no = ?'] = 0; // 不忽略签到
		$where['latest < ?'] = $day_time['begin']; // 今天没有签到
		$total_sign_tieba = $tieba_model->count($where); // 该条件下所有贴吧数量
		$limit = 100; // 100条100条循环拿
		$count = ceil($total_sign_tieba / $limit); // 循环100条的次数
		$else = 0; // 已遍历的数量
		for ($i = 1; $i <= $count; $i++) {
			$qs = $tieba_model->getList($limit, 0, $where, '*', 'id asc');
			$else += $qs['total'];
			$q = array();
			foreach ($qs['rows'] as $index => $qss) {
				$q[] = array(
					'id' => $qss['id'],
					'baidu_id' => $qss['baidu_id'],
					'fid' => $qss['fid'],
					'tieba' => $qss['tieba']
				);
			}
			shuffle($q);
			foreach ($q as $x) {
				self::doSign($x['tieba'], $x['id'], $x['baidu_id'], $x['fid']);
			}
		}
	}

	/**
	 * 执行一个会员的签到
	 */
	public static function doSignByUserId($user_id)
	{
		set_time_limit(0);
		$day_time = DateHelper::getDayTime(); // 今天开始的时间和结束的时间的时间戳
		//处理所有未签到的贴吧
		$tieba_model = new Model_Tieba();
		$where = array();
		$where['b.user_id = ?'] = $user_id; // 该会员
		$where['t.no = ?'] = 0; // 不忽略签到
		$where['t.latest < ?'] = $day_time['begin']; // 今天没有签到
		$total_sign_tieba = $tieba_model->getTiebasByJoinCount($where)[0]['c']; // 该条件下所有贴吧数量
		$limit = 100; // 100条100条循环拿
		$count = ceil($total_sign_tieba / $limit); // 循环100条的次数
		$else = 0; // 已遍历的数量
		for ($i = 1; $i <= $count; $i++) {
			$qs = $tieba_model->getTiebasByJoin($limit, 0, $where, '*', 't.id asc');
			$else += $qs['total'];
			$q = array();
			foreach ($qs['rows'] as $index => $qss) {
				$q[] = array(
					'id' => $qss['id'],
					'baidu_id' => $qss['baidu_id'],
					'fid' => $qss['fid'],
					'tieba' => $qss['tieba']
				);
			}
			shuffle($q);
			foreach ($q as $x) {
				self::doSign($x['tieba'], $x['id'], $x['baidu_id'], $x['fid']);
			}
		}
	}

	/**
	 * 执行一个贴吧的签到
	 */
	public static function doSignByTiebaId($tieba_id)
	{
		$tieba_model = new Model_Tieba();
		$x = $tieba_model->get($tieba_id);
		self::doSign($x['tieba'], $x['id'], $x['baidu_id'], $x['fid']);
	}

	/**
	 * 对一个贴吧执行完整的签到任务
	 */
	public static function doSign($kw, $id, $baidu_id, $fid)
	{
		$again_error_id = 160002; //重复签到错误代码
		$again_error_id_2 = 1101; //特殊的重复签到错误代码！！！签到过快=已签到
		$again_error_id_3 = 1102; //特殊的重复签到错误代码！！！签到过快=已签到
		$status_succ = false;

		$baiduid_model = new Model_BaiduId();
		$bdid = $baiduid_model->get($baidu_id, 'bduss');
		$ck = $bdid['bduss'];
		$kw = addslashes($kw);
		$tieba_model = new Model_Tieba();

		if (empty($fid)) {
			$fid = self::getFid($kw);
			$tieba_model->update($id, array('fid' => $fid));
		}

		//三种签到方式依次尝试
		$tbs = self::getTbs($ck);
		//客户端
		if ($status_succ === false) {
			$r = self::DoSign_Client($kw, $fid, $ck, $tbs);
			$v = json_decode($r, true);
			if ($v != $r && $v != NULL) {//decode失败时会直接返回原文或NULL
				$time = $v['time'];
				if (empty($v['error_code']) || $v['error_code'] == $again_error_id) {
					$status_succ = true;
				} else {
					$error_code = $v['error_code'];
					$error_msg = $v['error_msg'];
				}
			}
		}

		/*//手机网页
		if ($status_succ === false) {
			$r = self::DoSign_Mobile($kw, $fid, $ck, $tbs);
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

		//网页---尽量不用
		if ($status_succ === false) {
			if (self::DoSign_Default($kw, $fid, $ck) === true) {
				$status_succ = true;
			}
		}*/

		if ($status_succ === true) {
			$tieba_model->update($id, array('latest' => $time, 'status' => 0, 'last_error' => ''));
		} else {
			$tieba_model->update($id, array('latest' => $time, 'status' => $error_code, 'last_error' => $error_msg));
		}
	}

	/**
	 * 得到BDUSS
	 * @param int|string $baidu_id 贴吧用户PID
	 */
	/*public static function getCookie($baidu_id)
	{
		if (empty($baidu_id)) {
			return false;
		}
		$baiduid_model = new Model_BaiduId();
		$temp = $baiduid_model->get($baidu_id);
		return $temp['bduss'];
	}*/

	/**
	 * 得到TBS
	 */
	public static function getTbs($bduss)
	{
		$url = 'http://tieba.baidu.com/dc/common/tbs';
		DI()->curl->setHeader(array('User-Agent: fuck phone', 'Referer: http://tieba.baidu.com/', 'X-Forwarded-For: 115.28.1.' . mt_rand(1, 255)));
		DI()->curl->setCookie(array("BDUSS" => $bduss));
		$x = DI()->curl->json_get($url);
		return $x['tbs'];
	}

	/**
	 * 客户端签到
	 */
	public static function DoSign_Client($kw, $fid, $ck, $tbs)
	{
		$temp = array(
			'BDUSS' => $ck,
			'_client_id' => '03-00-DA-59-05-00-72-96-06-00-01-00-04-00-4C-43-01-00-34-F4-02-00-BC-25-09-00-4E-36',
			'_client_type' => '4',
			'_client_version' => '1.2.1.17',
			'_phone_imei' => '540b43b59d21b7a4824e1fd31b08e9a6',
			'fid' => $fid,
			'kw' => $kw,
			'net_type' => '3',
			'tbs' => $tbs
		);
		$x = '';
		foreach ($temp as $k => $v) {
			$x .= $k . '=' . $v;
		}
		$temp['sign'] = strtoupper(md5($x . 'tiebaclient!!!'));
		$url = 'http://c.tieba.baidu.com/c/c/forum/sign';
		DI()->curl->setHeader(array('Content-Type: application/x-www-form-urlencoded', 'User-Agent: Fucking iPhone/1.0 BadApple/99.1'));
		DI()->curl->setCookie(array("BDUSS" => $ck));
		return DI()->curl->post($url, $temp);
	}

	/**
	 * 手机网页签到
	 */
	public static function DoSign_Mobile($kw, $fid, $ck, $tbs)
	{
		$url = 'http://tieba.baidu.com/mo/q/sign?tbs=' . $tbs . '&kw=' . urlencode($kw) . '&is_like=1&fid=' . $fid;
		DI()->curl->setHeader(array('User-Agent: fuck phone', 'Referer: http://tieba.baidu.com/f?kw=' . $kw, 'Host: tieba.baidu.com', 'X-Forwarded-For: 115.28.1.' . mt_rand(1, 255), 'Origin: http://tieba.baidu.com', 'Connection: Keep-Alive'));
		DI()->curl->setCookie(array('BDUSS' => $ck));
		return DI()->curl->get($url);
	}

	/**
	 * 网页签到
	 */
	public static function DoSign_Default($kw, $fid, $ck)
	{
		$url = 'http://tieba.baidu.com/mo/m?kw=' . urlencode($kw) . '&fid=' . $fid;
		DI()->curl->setHeader(array('User-Agent: fuck phone', 'Referer: http://wapp.baidu.com/', 'Content-Type: application/x-www-form-urlencoded'));
		DI()->curl->setCookie(array('BDUSS' => $ck));
		$s = DI()->curl->get($url);
		preg_match('/\<td style=\"text-align:right;\"\>\<a href=\"(.*)\"\>签到\<\/a\>\<\/td\>\<\/tr\>/', $s, $s);
		if (isset($s[1])) {
			$url = 'http://tieba.baidu.com' . $s[1];
			DI()->curl->setHeader(array('Accept: text/html, application/xhtml+xml, */*', 'Accept-Language: zh-Hans-CN,zh-Hans;q=0.8,en-US;q=0.5,en;q=0.3', 'User-Agent: Fucking Phone'));
			DI()->curl->setCookie(array('BDUSS' => $ck));
			DI()->curl->get($url);
			//临时判断解决方案
			$url = 'http://tieba.baidu.com/mo/m?kw=' . urlencode($kw) . '&fid=' . $fid;
			DI()->curl->setHeader(array('User-Agent: fuck phone', 'Referer: http://wapp.baidu.com/', 'Content-Type: application/x-www-form-urlencoded'));
			DI()->curl->setCookie(array('BDUSS' => $ck));
			$s = DI()->curl->get($url);
			//如果找不到这段html则表示没有签到则stripos()返回false，同时is_bool()返回true，最终返回false
			return !is_bool(stripos($s, '<td style="text-align:right;"><span >已签到</span></td>'));
		} else {
			return true;
		}
	}


}
