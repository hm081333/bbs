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
			),
		);
	}

	public function login()
	{
		if ($this->action == 'post') {
			$user_model = new Model_User();
			$user = $user_model->getInfo(array('username' => $this->username), 'id, username, password, auth');
			if ($user === false) {
				throw new PhalApi_Exception_Error(T('用户名不存在'), 1);// 抛出客户端错误 T标签翻译
			} elseif (!password_verify($this->password, $user['password'])) {
				throw new PhalApi_Exception_Error(T('密码错误'), 1);// 抛出客户端错误 T标签翻译
			} else {
				//将用户名存如SESSION中
				$_SESSION['user_id'] = $user['id'];
				$_SESSION['username'] = $user['username'];
				$_SESSION['user_auth'] = $user['auth'];
				DI()->response->setMsg(T('登陆成功'));
				return;
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

			$user_model = new Model_User();
			$insert_data = array();
			$insert_data['username'] = $this->username;
			$insert_data['password'] = password_hash($this->password, PASSWORD_BCRYPT);
			$insert_data['email'] = $this->email;
			$insert_data['realname'] = $this->realname;
			$rs = $user_model->insert($insert_data);
			unset($insert_data);
			if ($rs) {
				$_SESSION['user_id'] = $rs;
				$_SESSION['username'] = $this->username;
				$_SESSION['user_auth'] = 0;
				DI()->response->setMsg(T('注册成功'));
				return;
			} else {
				throw new PhalApi_Exception_InternalServerError(T('注册失败'), 2);// 抛出服务端错误
			}
		} else {
			DI()->view->show('register');
		}
	}

	public function logoff()
	{
		$_SESSION = array();
		session_unset();
		//清空SESSION
		session_destroy();
		//跳转页面
		header("Location: ./");
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
			$rs = $user_domain->edit_Member($this->user_id, $this->password, $this->email, $this->realname);
			DI()->response->setMsg(T('修改成功'));
			return;
		} else {
			$user_domain = new Domain_User();
			$user = $user_domain->userInfo($this->user_id);
			DI()->view->assign(array('user' => $user['info']));
			DI()->view->show('edit_member');
		}
	}


}
