<?php

/**
 * 用户信息类
 */
class Api_User extends PhalApi_Api
{

	public function getRules()
	{
		return array(
			'logoff' => array(),
			'login' => array(
				'action' => array('name' => 'action', 'type' => 'string', 'default' => 'view', 'require' => true, 'desc' => '操作'),
				'username' => array('name' => 'username', 'type' => 'string', 'require' => false, 'desc' => '用户名'),
				'password' => array('name' => 'password', 'type' => 'string', 'require' => false, 'desc' => '用户密码'),
			),
			'register' => array(
				'action' => array('name' => 'action', 'type' => 'string', 'default' => 'view', 'require' => true, 'desc' => '操作'),
				'username' => array('name' => 'username', 'type' => 'string', 'require' => false, 'desc' => '用户名'),
				'password' => array('name' => 'password', 'type' => 'string', 'require' => false, 'desc' => '用户密码'),
				'email' => array('name' => 'email', 'type' => 'string', 'require' => false, 'desc' => '用户邮箱'),
				'realname' => array('name' => 'realname', 'type' => 'string', 'require' => false, 'desc' => '用户姓名'),
				'birthdate' => array('name' => 'birthdate', 'type' => 'string', 'require' => false, 'desc' => '用户生日'),
			),
			'user_Info' => array(
				'user_id' => array('name' => 'user_id', 'type' => 'int', 'require' => false, 'desc' => '用户ID')
			),
			'edit_Member' => array(
				'action' => array('name' => 'action', 'type' => 'string', 'default' => 'view', 'require' => true, 'desc' => '操作'),
				'user_id' => array('name' => 'user_id', 'type' => 'int', 'require' => false, 'desc' => '用户ID'),
				'password' => array('name' => 'password', 'type' => 'string', 'require' => false, 'desc' => '用户密码'),
				'email' => array('name' => 'email', 'type' => 'string', 'require' => false, 'desc' => '用户邮箱'),
				'realname' => array('name' => 'realname', 'type' => 'string', 'require' => false, 'desc' => '用户姓名'),
				'secret' => array('name' => 'secret', 'type' => 'string', 'require' => false, 'desc' => '谷歌身份验证器密钥'),
				'check' => array('name' => 'check', 'type' => 'string', 'require' => false, 'desc' => '身份验证器验证成功'),
			),
		);
	}

	public function login()
	{
		if ($this->action == 'post') {
			$user_model = new Model_User();
			if (empty($this->username)) {
				throw new PhalApi_Exception_Error(T('请输入用户名'), 1);// 抛出普通错误 T标签翻译
			} elseif (empty($this->password)) {
				throw new PhalApi_Exception_Error(T('请输入密码'), 1);// 抛出普通错误 T标签翻译
			}
			$user = $user_model->getInfo(array('username' => $this->username), 'id, username, password, auth');
			if ($user === false) {
				throw new PhalApi_Exception_Error(T('用户名不存在'), 1);// 抛出客户端错误 T标签翻译
			} elseif (!Domain_Common::verify($this->password, $user['password'])) {
				throw new PhalApi_Exception_Error(T('密码错误'), 1);// 抛出客户端错误 T标签翻译
			} else {
				//将用户名存如SESSION中
				$_SESSION['user_id'] = $user['id'];
				$_SESSION['user_name'] = $user['username'];
				$_SESSION['user_auth'] = $user['auth'];
				DI()->response->setMsg(T('登陆成功'));
				return 'user';
			}
		} else {
			DI()->view->show('login');
		}
	}

	public function register()
	{
		if ($this->action == 'post') {
			$user_model = new Model_User();
			if (empty($this->username)) {
				throw new PhalApi_Exception_Error(T('请输入用户名'), 1);// 抛出普通错误 T标签翻译
			} elseif (empty($this->password)) {
				throw new PhalApi_Exception_Error(T('请输入密码'), 1);// 抛出普通错误 T标签翻译
			} elseif (empty($this->email)) {
				throw new PhalApi_Exception_Error(T('请输入邮箱'), 1);// 抛出普通错误 T标签翻译
			} elseif (empty($this->realname)) {
				throw new PhalApi_Exception_Error(T('请输入姓名'), 1);// 抛出普通错误 T标签翻译
			} elseif (empty($this->birthdate)) {
				throw new PhalApi_Exception_Error(T('请选择生日'), 1);// 抛出普通错误 T标签翻译
			}

			$user = $user_model->getInfo(array('username' => $this->username), 'id');
			if ($user !== false) {
				throw new PhalApi_Exception_Error(T('用户名已注册'), 1);// 抛出普通错误 T标签翻译
			} elseif (strlen($this->password) < 6) {
				throw new PhalApi_Exception_Error(T('请输入6位长度密码'), 1);// 抛出普通错误 T标签翻译
			} elseif ((Domain_Common::checkEmail($this->email)) === false) {
				throw new PhalApi_Exception_Error(T('邮箱格式不正确'), 1);// 抛出普通错误 T标签翻译
			}
			$email = $user_model->getInfo(array('email' => $this->email), 'id');
			if ($email !== false) {
				throw new PhalApi_Exception_Error(T('邮箱已注册'), 1);// 抛出普通错误 T标签翻译
			}
			$birth_time = strtotime($this->birthdate);
			$user_model = new Model_User();
			$insert_data = array();
			$insert_data['username'] = $this->username;
			$insert_data['password'] = Domain_Common::hash($this->password);
			$insert_data['email'] = $this->email;
			$insert_data['realname'] = $this->realname;
			$insert_data['regdate'] = new NotORM_Literal("NOW()");
			$insert_data['reg_time'] = NOW_TIME;
			$insert_data['birth_time'] = $birth_time;
			$rs = $user_model->insert($insert_data);
			unset($insert_data);
			if ($rs) {
				$_SESSION['user_id'] = $rs;
				$_SESSION['user_name'] = $this->username;
				$_SESSION['user_auth'] = 0;
				DI()->response->setMsg(T('注册成功'));
				return 'user';
			} else {
				throw new PhalApi_Exception_InternalServerError(T('注册失败'), 2);// 抛出服务端错误
			}
		} else {
			DI()->view->show('register');
		}
	}

	public function logoff()
	{
		/*$_SESSION = array();
		session_unset();
		//清空SESSION
		session_destroy();*/
		//仅清除会员相关session
		if (isset($_SESSION['user_id'])) {
			unset($_SESSION['user_id']);
		}
		if (isset($_SESSION['user_name'])) {
			unset($_SESSION['user_name']);
		}
		if (isset($_SESSION['user_auth'])) {
			unset($_SESSION['user_auth']);
		}
		DI()->response->setMsg(T('退出登录成功'));
		//跳转页面
		//header("Location: ./");
	}

	public function user_Info()
	{
		$user_domain = new Domain_User();
		$user = $user_domain->userInfo($this->user_id);
		DI()->view->assign(array('user' => $user));
		DI()->view->show('user_info');
	}

	public function edit_Member()
	{
		if ($this->action == 'post') {
			$user_domain = new Domain_User();
			$rs = $user_domain->edit_Member($this->user_id, $this->password, $this->email, $this->realname, false, false, $this->secret, $this->check);
			DI()->response->setMsg(T('修改成功'));
			return;
		} else {
			$user_domain = new Domain_User();
			$user = $user_domain->userInfo($_SESSION['user_id']);
			//密钥
			$secret = Domain_Common::create_Google_Auth();
			//二维码
			$qrCodeUrl = Domain_Common::get_Google_Auth_Url($secret);
			DI()->view->assign(array('user' => $user['info'], 'secret' => $secret, 'qrCodeUrl' => $qrCodeUrl));
			DI()->view->show('edit_member');
		}
	}


}
