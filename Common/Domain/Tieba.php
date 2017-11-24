<?php

class Domain_Tieba
{

	/**
	 * 删除贴吧
	 * @param $tieba_id
	 * @return bool
	 * @throws PhalApi_Exception_Error
	 */
	public static function deleteTieba($tieba_id)
	{
		$tieba_model = new Model_Tieba();
		$result = $tieba_model->delete($tieba_id);
		if ($result === false) {
			throw new PhalApi_Exception_Error(T('删除失败'));
		}
		return true;
	}

	/**
	 * 忽略签到
	 * @param $tieba_id
	 * @param $no
	 * @return bool
	 * @throws PhalApi_Exception_Error
	 */
	public static function noSignTieba($tieba_id, $no)
	{
		$tieba_model = new Model_Tieba();
		$result = $tieba_model->update($tieba_id, array('no' => $no));
		if ($result === false) {
			throw new PhalApi_Exception_Error(T('操作失败'));
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
			throw new PhalApi_Exception_Error('您的 BDUSS Cookie 信息有误，请核验后重新绑定');
		}
		$baiduid_model = new Model_BaiduId();
		$check = $baiduid_model->getInfo(array('name' => $baidu_name), 'id');
		if ($check) {
			throw new PhalApi_Exception_Error('该账号已经绑定过了');
		}
		$insert_rs = $baiduid_model->insert(array('user_id' => $user_id, 'bduss' => $bduss, 'name' => $baidu_name));
		if ($insert_rs === false) {
			throw new PhalApi_Exception_Error('插入表失败');
		}
		DI()->response->setMsg('添加新Bduss成功');
		return array('url' => '?service=Default.Index');
	}

	/**
	 * 获取一个bduss对应的百度用户名
	 * @param string $bduss BDUSS
	 * @return string|bool 百度用户名，失败返回FALSE
	 */
	public static function getBaiduId($bduss)
	{
		$url = 'http://wapp.baidu.com/';
		DI()->curl->setCookie(array('BDUSS' => $bduss, 'BAIDUID' => strtoupper(md5(SERVER_TIME))));
		$data = DI()->curl->get($url);
		return urldecode(DI()->tool->textMiddle($data, 'i?un=', '">'));
	}

	/**
	 * 扫描指定用户的所有贴吧并储存--用于一键刷新
	 * @param UserID，如果留空，表示当前用户的UID
	 */
	public static function scanTiebaByUser($user_id = '')
	{
		$baiduid_model = new Model_BaiduId();
		set_time_limit(0);
		$bx = $baiduid_model->getListByWhere(array('user_id' => $user_id));
		foreach ($bx as $by) {
			$upid = $by['id'];
			$bduss[$upid] = $by['bduss'];
		}
		foreach ($bduss as $pid => $ubduss) {
			self::scanTiebaByPid($pid);
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
				foreach ($ngf as $v) {
					$vn = addslashes(htmlspecialchars($v['name']));
					$ist = $tieba_model->getCountByWhere(array('baidu_id' => $pid, 'tieba' => $vn));
					if ($ist == 0) {
						$a++;
						$tieba_model->insert(array('baidu_id' => $pid, 'fid' => $v['id'], 'user_id' => $user_id, 'tieba' => $vn, 'refresh_time' => $refresh_time));
					}
				}
				if ($a > 0) {
					$baiduid_model->update($pid, array('refresh_time' => $refresh_time));
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
	private static function getUserid($name)
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
		$url = 'http://c.tieba.baidu.com/c/f/forum/like';
		$data = array(
			'_client_id' => 'wappc_' . SERVER_TIME . '_' . '258',
			'_client_type' => 2,
			'_client_version' => '6.5.8',
			'_phone_imei' => '357143042411618',
			'from' => 'baidu_appstore',
			'is_guest' => 1,
			'model' => 'H60-L01',
			'page_no' => $pn,
			'page_size' => 200,
			'timestamp' => SERVER_TIME . '903',
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
		DI()->curl->setHeader(array('User-Agent: fuck phone', 'Referer: http://wapp.baidu.com/', 'Content-Type: application/x-www-form-urlencoded', 'Cookie:BAIDUID=' . strtoupper(md5(SERVER_TIME))));
		$s = DI()->curl->get($url);
		$x = DI()->tool->easy_match('<input type="hidden" name="fid" value="*"/>', $s);
		if (isset($x[1])) {
			return $x[1];
		} else {
			return false;
		}
	}

	/**
	 * 执行全部贴吧用户的签到任务
	 */
	public static function doRetryAll()
	{
		set_time_limit(0);
		//处理所有签到出错的贴吧
		$tieba_model = new Model_Tieba();
		$where = array();
		$where['no = ?'] = 0; // 不忽略签到
		$where['status != ?'] = 0; // 签到状态不为0==签到出错
		$total_sign_tieba = $tieba_model->getCountByWhere($where); // 该条件下所有贴吧数量
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
					'tieba' => $qss['tieba'],
				);
			}
			shuffle($q);
			foreach ($q as $x) {
				self::doSign($x['tieba'], $x['id'], $x['baidu_id'], $x['fid']);
			}
		}
	}

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
		$total_sign_tieba = $tieba_model->getCountByWhere($where); // 该条件下所有贴吧数量
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
		$total_sign_tieba = $tieba_model->getCountByWhere($where); // 该条件下所有贴吧数量
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
			$fid = self::getFid($kw);//贴吧唯一ID
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
			//$tieba_model->update($id, array('status' => $error_code, 'last_error' => $error_msg));
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


	/**
	 * CURL整合--返回数组
	 * @param $url
	 * @param int $post
	 * @param int $referer
	 * @param int $cookie
	 * @param int $header
	 * @param int $ua
	 * @param int $nobaody
	 * @return mixed
	 * @throws PhalApi_Exception_InternalServerError
	 */
	private static function get_curl($url, $post = 0, $referer = 1, $cookie = 0, $header = 0, $ua = 0, $nobaody = 0)
	{
		$httpheader = array();
		$httpheader[] = "Accept:application/json";
		$httpheader[] = "Accept-Encoding:gzip,deflate,sdch";
		$httpheader[] = "Accept-Language:zh-CN,zh;q=0.8";
		$httpheader[] = "Connection:close";
		DI()->curl->setHeader($httpheader);
		$option = array();
		$option[CURLOPT_SSL_VERIFYPEER] = FALSE;
		$option[CURLOPT_SSL_VERIFYHOST] = FALSE;
		if ($header) {
			$option[CURLOPT_HEADER] = TRUE;
		}
		if ($cookie) {
			$option[CURLOPT_COOKIE] = $cookie;
		}
		if ($referer) {
			$option[CURLOPT_REFERER] = 'https://wappass.baidu.com/';
		}
		if ($ua) {
			$option[CURLOPT_USERAGENT] = $ua;
		} else {
			$option[CURLOPT_USERAGENT] = 'Mozilla/5.0 (Linux; Android 4.4.2; H650 Build/KOT49H) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/30.0.0.0 Mobile Safari/537.36';
			//$option[CURLOPT_USERAGENT] = 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36';
		}
		if ($nobaody) {
			$option[CURLOPT_NOBODY] = 1;
		}
		$option[CURLOPT_ENCODING] = 'gzip';
		DI()->curl->setOption($option);
		if ($post) {
			$ret = DI()->curl->post($url, $post);
		} else {
			$ret = DI()->curl->get($url);
		}
		if (empty($ret)) {
			throw new PhalApi_Exception_Error(T('连接到百度服务器失败'));
		}
		return $ret;
	}

	//获取ServerTime
	public static function serverTime()
	{
		$url = 'https://wappass.baidu.com/wp/api/security/antireplaytoken?tpl=tb&v=' . SERVER_TIME . '0000';
		$data = self::get_curl($url, 0);
		$arr = json_decode($data, true);
		if ($arr['errno'] == 110000) {
			return array('code' => 0, 'time' => $arr['time']);
		} else {
			return array('code' => -1, 'msg' => $arr['errmsg']);
		}
	}

	//获取验证码图片
	public static function getVCPic($vcodestr)
	{
		$url = 'https://wappass.baidu.com/cgi-bin/genimage?' . $vcodestr . '&v=' . SERVER_TIME . '0000';
		return self::get_curl($url, 0);
	}

	//普通登录操作
	public static function login($time, $user, $pwd, $p, $vcode = null, $vcodestr = null)
	{
		if (empty($user)) {
			throw new PhalApi_Exception_BadRequest(T('用户名不能为空'));
		}
		if (empty($pwd)) {
			throw new PhalApi_Exception_BadRequest(T('pwd不能为空'));
		}
		if (empty($p)) {
			throw new PhalApi_Exception_BadRequest(T('密码不能为空'));
		}
		if ($vcode == 'null') {
			$vcode = '';
		}
		if ($vcodestr == 'null') {
			$vcodestr = '';
		}

		$url = 'https://wappass.baidu.com/wp/api/login?v=' . SERVER_TIME . '0000';
		$post = 'username=' . $user . '&code=&password=' . $p . '&verifycode=' . $vcode . '&clientfrom=native&tpl=tb&login_share_strategy=choice&client=android&adapter=3&t=' . SERVER_TIME . '0000&act=bind_mobile&loginLink=0&smsLoginLink=1&lPFastRegLink=0&fastRegLink=1&lPlayout=0&loginInitType=0&lang=zh-cn&regLink=1&action=login&loginmerge=1&isphone=0&dialogVerifyCode=&dialogVcodestr=&dialogVcodesign=&gid=660BDF6-30E5-4A83-8EAC-F0B4752E1C4B&vcodestr=' . $vcodestr . '&countrycode=&servertime=' . $time . '&logLoginType=sdk_login&passAppHash=&passAppVersion=';
		$data = self::get_curl($url, $post);
		$arr = json_decode($data, true);
		if (array_key_exists('errInfo', $arr) && $arr['errInfo']['no'] == '0') {
			if (!empty($arr['data']['loginProxy'])) {
				$data = self::get_curl($arr['data']['loginProxy'], 0);
				$arr = json_decode($data, true);
			}
			$data = $arr['data']['xml'];
			preg_match('!<uname>(.*?)</uname>!i', $data, $user);
			preg_match('!<uid>(.*?)</uid>!i', $data, $uid);
			preg_match('!<portrait>(.*?)</portrait>!i', $data, $face);
			preg_match('!<displayname>(.*?)</displayname>!i', $data, $displayname);
			preg_match('!<bduss>(.*?)</bduss>!i', $data, $bduss);
			preg_match('!<ptoken>(.*?)</ptoken>!i', $data, $ptoken);
			preg_match('!<stoken>(.*?)</stoken>!i', $data, $stoken);
			return array('code' => 0, 'uid' => $uid[1], 'user' => $user[1], 'displayname' => $displayname[1], 'face' => $face[1], 'bduss' => $bduss[1], 'ptoken' => $ptoken[1], 'stoken' => $stoken[1]);
		} elseif ($arr['errInfo']['no'] == '310006' || $arr['errInfo']['no'] == '500001' || $arr['errInfo']['no'] == '500002') {
			return array('code' => $arr['errInfo']['no'], 'msg' => $arr['errInfo']['msg'], 'vcodestr' => $arr['data']['codeString']);
		} elseif ($arr['errInfo']['no'] == '400023') {
			return array('code' => $arr['errInfo']['no'], 'msg' => $arr['errInfo']['msg'], 'type' => $arr['data']['showType'], 'phone' => $arr['data']['phone'], 'email' => $arr['data']['email'], 'lstr' => $arr['data']['lstr'], 'ltoken' => $arr['data']['ltoken']);
		} elseif (array_key_exists('errInfo', $arr)) {
			return array('code' => $arr['errInfo']['no'], 'msg' => $arr['errInfo']['msg']);
		} else {
			throw new PhalApi_Exception_Error(T('登录失败，原因未知'));
		}
	}

	//登录异常时发送手机/邮件验证码
	public static function sendCode($type, $lstr, $ltoken)
	{
		$url = 'https://wappass.baidu.com/wp/login/sec?ajax=1&v=' . SERVER_TIME . '0000&vcode=&clientfrom=native&tpl=tb&login_share_strategy=choice&client=android&adapter=3&t=' . SERVER_TIME . '0000&act=bind_mobile&loginLink=0&smsLoginLink=1&lPFastRegLink=0&fastRegLink=1&lPlayout=0&loginInitType=0&lang=zh-cn&regLink=1&action=login&loginmerge=1&isphone=0&dialogVerifyCode=&dialogVcodestr=&dialogVcodesign=&gid=660BDF6-30E5-4A83-8EAC-F0B4752E1C4B&showtype=' . $type . '&lstr=' . rawurlencode($lstr) . '&ltoken=' . $ltoken;
		$data = self::get_curl($url, 0);
		$arr = json_decode($data, true);
		if (array_key_exists('errInfo', $arr) && $arr['errInfo']['no'] == '0') {
			return array('code' => 0);
		} elseif (array_key_exists('errInfo', $arr)) {
			return array('code' => $arr['errInfo']['no'], 'msg' => $arr['errInfo']['msg']);
		} else {
			throw new PhalApi_Exception_Error(T('发生验证码失败，原因未知'));
		}
	}


	//登录异常时登录操作
	public static function login2($type, $lstr, $ltoken, $vcode)
	{
		if (empty($type)) {
			throw new PhalApi_Exception_BadRequest(T('type不能为空'));
		}
		if (empty($lstr)) {
			throw new PhalApi_Exception_BadRequest(T('lstr不能为空'));
		}
		if (empty($ltoken)) {
			throw new PhalApi_Exception_BadRequest(T('ltoken不能为空'));
		}
		if (empty($vcode)) {
			throw new PhalApi_Exception_BadRequest(T('vcode不能为空'));
		}

		$url = 'https://wappass.baidu.com/wp/login/sec?type=2&v=' . SERVER_TIME . '0000';
		$post = 'vcode=' . $vcode . '&clientfrom=native&tpl=tb&login_share_strategy=choice&client=android&adapter=3&t=' . SERVER_TIME . '0000&act=bind_mobile&loginLink=0&smsLoginLink=1&lPFastRegLink=0&fastRegLink=1&lPlayout=0&loginInitType=0&lang=zh-cn&regLink=1&action=login&loginmerge=1&isphone=0&dialogVerifyCode=&dialogVcodestr=&dialogVcodesign=&gid=660BDF6-30E5-4A83-8EAC-F0B4752E1C4B&showtype=' . $type . '&lstr=' . rawurlencode($lstr) . '&ltoken=' . $ltoken;
		$data = self::get_curl($url, $post);
		$arr = json_decode($data, true);
		if (array_key_exists('errInfo', $arr) && $arr['errInfo']['no'] == '0') {
			if (!empty($arr['data']['loginProxy'])) {
				$data = self::get_curl($arr['data']['loginProxy'], 0);
				$arr = json_decode($data, true);
			}
			$data = $arr['data']['xml'];
			preg_match('!<uname>(.*?)</uname>!i', $data, $user);
			preg_match('!<uid>(.*?)</uid>!i', $data, $uid);
			preg_match('!<portrait>(.*?)</portrait>!i', $data, $face);
			preg_match('!<displayname>(.*?)</displayname>!i', $data, $displayname);
			preg_match('!<bduss>(.*?)</bduss>!i', $data, $bduss);
			preg_match('!<ptoken>(.*?)</ptoken>!i', $data, $ptoken);
			preg_match('!<stoken>(.*?)</stoken>!i', $data, $stoken);
			return array('code' => 0, 'uid' => $uid[1], 'user' => $user[1], 'displayname' => $displayname[1], 'face' => $face[1], 'bduss' => $bduss[1], 'ptoken' => $ptoken[1], 'stoken' => $stoken[1]);
		} elseif (array_key_exists('errInfo', $arr)) {
			return array('code' => $arr['errInfo']['no'], 'msg' => $arr['errInfo']['msg']);
		} else {
			throw new PhalApi_Exception_Error(T('登录失败，原因未知'));
		}
	}

	//检测是否需要验证码
	public static function checkVC($user)
	{
		if (empty($user)) {
			throw new PhalApi_Exception_BadRequest(T('请先输入用户名'));
		}
		$url = 'https://wappass.baidu.com/wp/api/login/check?tt=' . SERVER_TIME . '9117&username=' . $user . '&countrycode=&clientfrom=wap&sub_source=leadsetpwd&tpl=tb';
		$data = self::get_curl($url, 0);
		$arr = json_decode($data, true);
		if ($arr['errInfo'] && $arr['errInfo']['no'] == '0' && empty($arr['data']['codeString'])) {
			return array('code' => 0);
		} elseif ($arr['errInfo'] && $arr['errInfo']['no'] == '0') {
			return array('code' => 1, 'vcodestr' => $arr['data']['codeString']);
		} else {
			return array('code' => $arr['errInfo']['no'], 'msg' => $arr['errInfo']['msg']);
		}
	}


	//手机验证码登录，获取手机号是否存在
	public static function getPhone($phone)
	{
		if (empty($phone)) {
			throw new PhalApi_Exception_BadRequest(T('请先输入手机号'));
		}
		if (strlen($phone) != 11) {
			throw new PhalApi_Exception_BadRequest(T('请输入正确的手机号'));
		}
		$phone2 = '';
		for ($i = 0; $i < 11; $i++) {
			$phone2 .= $phone[$i];
			if ($i == 2 || $i == 6) $phone2 .= '+';
		}
		$url = 'https://wappass.baidu.com/wp/api/security/getphonestatus?v=' . SERVER_TIME . '0000';
		$post = 'mobilenum=' . $phone2 . '&clientfrom=native&tpl=tb&login_share_strategy=choice&client=android&adapter=3&t=' . SERVER_TIME . '0000&act=bind_mobile&loginLink=0&smsLoginLink=1&lPFastRegLink=0&fastRegLink=1&lPlayout=0&lang=zh-cn&regLink=1&action=login&loginmerge=1&isphone=0&dialogVerifyCode=&dialogVcodestr=&dialogVcodesign=&gid=E528690-4ADF-47A5-BA87-1FD76D2583EA&agreement=1&vcodesign=&vcodestr=&sms=1&username=' . $phone . '&countrycode=';
		$data = self::get_curl($url, $post);
		$arr = json_decode($data, true);
		if ($arr['errInfo'] && $arr['errInfo']['no'] == '0') {
			return array('code' => 0, 'msg' => $arr['errInfo']['msg']);
		} else {
			return array('code' => $arr['errInfo']['no'], 'msg' => $arr['errInfo']['msg']);
		}
	}


	//手机验证码登录，发送验证码
	public static function sendSms($phone, $vcode = null, $vcodestr = null, $vcodesign = null)
	{
		if (empty($phone)) {
			throw new PhalApi_Exception_BadRequest(T('请先输入手机号'));
		}
		if (strlen($phone) != 11) {
			throw new PhalApi_Exception_BadRequest(T('请输入正确的手机号'));
		}
		if ($vcode == 'null') {
			$vcode = '';
		}
		if ($vcodestr == 'null') {
			$vcodestr = '';
		}
		if ($vcodesign == 'null') {
			$vcodesign = '';
		}
		$url = 'https://wappass.baidu.com/wp/api/login/sms?v=' . SERVER_TIME . '0000';
		$post = 'username=' . $phone . '&tpl=tb&clientfrom=native&countrycode=&gid=E528690-4ADF-47A5-BA87-1FD76D2583EA&dialogVerifyCode=' . $vcode . '&vcodesign=' . $vcodesign . '&vcodestr=' . $vcodestr;
		$data = self::get_curl($url, $post);
		$arr = json_decode($data, true);
		if ($arr['errInfo'] && $arr['errInfo']['no'] == '0') {
			return array('code' => 0, 'msg' => $arr['errInfo']['msg']);
		} elseif ($arr['errInfo']['no'] == '50020') {
			return array('code' => $arr['errInfo']['no'], 'msg' => $arr['errInfo']['msg'], 'vcodestr' => $arr['data']['vcodestr'], 'vcodesign' => $arr['data']['vcodesign']);
		} else {
			return array('code' => $arr['errInfo']['no'], 'msg' => $arr['errInfo']['msg']);
		}
	}


	//手机验证码登录操作
	public static function login3($phone, $smsvc)
	{
		if (empty($phone)) {
			throw new PhalApi_Exception_BadRequest(T('手机号不能为空'));
		}
		if (empty($smsvc)) {
			throw new PhalApi_Exception_BadRequest(T('验证码不能为空'));
		}

		$url = 'https://wappass.baidu.com/wp/api/login?v=' . SERVER_TIME . '0000';
		$post = 'smsvc=' . $smsvc . '&clientfrom=native&tpl=tb&login_share_strategy=choice&client=android&adapter=3&t=' . SERVER_TIME . '0000&act=bind_mobile&loginLink=0&smsLoginLink=1&lPFastRegLink=0&fastRegLink=1&lPlayout=0&lang=zh-cn&regLink=1&action=login&loginmerge=&isphone=0&dialogVerifyCode=&dialogVcodestr=&dialogVcodesign=&gid=E528690-4ADF-47A5-BA87-1FD76D2583EA&agreement=1&vcodesign=&vcodestr=&smsverify=1&sms=1&mobilenum=' . $phone . '&username=' . $phone . '&countrycode=&passAppHash=&passAppVersion=';
		$data = self::get_curl($url, $post);
		$arr = json_decode($data, true);
		if (array_key_exists('errInfo', $arr) && $arr['errInfo']['no'] == '0') {
			if (!empty($arr['data']['loginProxy'])) {
				$data = self::get_curl($arr['data']['loginProxy'], 0);
				$arr = json_decode($data, true);
			}
			$data = $arr['data']['xml'];
			preg_match('!<uname>(.*?)</uname>!i', $data, $user);
			preg_match('!<uid>(.*?)</uid>!i', $data, $uid);
			preg_match('!<portrait>(.*?)</portrait>!i', $data, $face);
			preg_match('!<displayname>(.*?)</displayname>!i', $data, $displayname);
			preg_match('!<bduss>(.*?)</bduss>!i', $data, $bduss);
			preg_match('!<ptoken>(.*?)</ptoken>!i', $data, $ptoken);
			preg_match('!<stoken>(.*?)</stoken>!i', $data, $stoken);
			return array('code' => 0, 'uid' => $uid[1], 'user' => $user[1], 'displayname' => $displayname[1], 'face' => $face[1], 'bduss' => $bduss[1], 'ptoken' => $ptoken[1], 'stoken' => $stoken[1]);
		} elseif (array_key_exists('errInfo', $arr)) {
			return array('code' => $arr['errInfo']['no'], 'msg' => $arr['errInfo']['msg']);
		} else {
			throw new PhalApi_Exception_Error(T('登录失败，原因未知'));
		}
	}


	//获取扫码登录二维码
	public static function getQRCode()
	{
		$url = 'https://passport.baidu.com/v2/api/getqrcode?lp=pc&gid=07D9D20-91EB-43D8-8553-16A98A0B24AA&apiver=v3&tt=' . SERVER_TIME . '0000&callback=callback';
		$data = self::get_curl($url, 0, 'https://passport.baidu.com/v2/?login');
		preg_match('/callback\((.*?)\)/', $data, $match);
		$arr = json_decode($match[1], true);
		if (array_key_exists('errno', $arr) && $arr['errno'] == 0) {
			return array('code' => 0, 'imgurl' => $arr['imgurl'], 'sign' => $arr['sign'], 'link' => 'https://wappass.baidu.com/wp/?qrlogin&t=' . SERVER_TIME . '&error=0&sign=' . $arr['sign'] . '&cmd=login&lp=pc&tpl=&uaonly=');
		} else {
			return array('code' => $arr['errno'], 'msg' => '获取二维码失败');
		}
	}

	//扫码登录操作
	public static function qRLogin($sign)
	{
		throw new PhalApi_Exception_BadRequest('sign不能为空');
		if (empty($sign)) {
		}
		$url = 'https://passport.baidu.com/channel/unicast?channel_id=' . $sign . '&tpl=pp&gid=07D9D20-91EB-43D8-8553-16A98A0B24AA&apiver=v3&tt=' . SERVER_TIME . '0000&callback=callback';
		$data = self::get_curl($url);
		preg_match('/callback\((.*?)\)/', $data, $match);
		$arr = json_decode($match[1], true);
		if (array_key_exists('errno', $arr) && $arr['errno'] == 0) {
			$arr = json_decode($arr['channel_v'], true);
			$data = self::get_curl('https://passport.baidu.com/v2/api/bdusslogin?bduss=' . $arr['v'] . '&u=https%3A%2F%2Fpassport.baidu.com%2F&qrcode=1&tpl=pp&apiver=v3&tt=' . SERVER_TIME . '0000&callback=callback', false, true, false, true);
			preg_match('/callback\((.*?)\)/', $data, $match);
			$arr = json_decode($match[1], true);
			if (array_key_exists('errInfo', $arr) && $arr['errInfo']['no'] == '0') {
				$data = str_replace('=deleted', '', $data);
				preg_match('!BDUSS=(.*?);!i', $data, $bduss);

				$user_id = $_SESSION['user_id'];
				return Domain_Tieba::addBduss($user_id, $bduss[1]);

				//preg_match('!PTOKEN=(.*?);!i', $data, $ptoken);
				//preg_match('!STOKEN=(.*?);!i', $data, $stoken);
				//$userid = self::getUserid($arr['data']['userName']);
				//return array('code' => 0, 'uid' => $userid, 'user' => $arr['data']['userName'], 'displayname' => $arr['data']['displayname'], 'mail' => $arr['data']['mail'], 'phone' => $arr['data']['phoneNumber'], 'bduss' => $bduss[1], 'ptoken' => $ptoken[1], 'stoken' => $stoken[1]);
			} elseif (array_key_exists('errInfo', $arr)) {
				throw new PhalApi_Exception_BadRequest($arr['errInfo']['msg']);
				//return array('code' => $arr['errInfo']['no'], 'msg' => $arr['errInfo']['msg']);
			} else {
				throw new PhalApi_Exception_BadRequest('登录失败，原因未知');
				//return array('code' => '-1', 'msg' => '登录失败，原因未知');
			}
		} elseif (array_key_exists('errno', $arr)) {
			throw new PhalApi_Exception_BadRequest('未检测到登录状态');
			//return array('code' => $arr['errno']);
		} else {
			throw new PhalApi_Exception_BadRequest('登录失败，原因未知');
			//return array('code' => '-1', 'msg' => '登录失败，原因未知');
		}
	}


}
