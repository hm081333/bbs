<?php

/**
 * 用户信息类
 */
class Api_User extends PhalApi_Api
{

	public function getRules()
	{
		return array(
			'login' => array(
//				'userId' => array('name' => 'user_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '用户ID'),
			),
			'logoff' => array(
//                'userIds' => array('name' => 'user_ids', 'type' => 'array', 'format' => 'explode', 'require' => true, 'desc' => '用户ID，多个以逗号分割'),
			),
			'chklogin' => array(
                'username' => array('name' => 'username', 'type' => 'string', 'require' => true, 'desc' => '用户名'),
				'password' => array('name' => 'password', 'type' => 'string', 'require' => true, 'desc' => '用户密码'),
			),
			'register' => array(
			),
			'doRegister' => array(
				'username' => array('name' => 'username', 'type' => 'string', 'require' => true, 'desc' => '用户名'),
				'password' => array('name' => 'password', 'type' => 'string', 'require' => true, 'desc' => '用户密码'),
				'email' => array('name' => 'email', 'type' => 'string', 'require' => true, 'desc' => '用户邮箱'),
				'realname' => array('name' => 'realname', 'type' => 'string', 'require' => true, 'desc' => '用户姓名'),
			),
		);
	}

	public function login()
	{
		DI()->view->show('login');
	}

	public function chklogin()
	{
		$user_model = new Model_User();
		$user = $user_model->getInfo(array('username' => $this->username), 'id, username, password, auth');
		if ($user === false) {
			throw new PhalApi_Exception_Error(T('用户名不存在'), 1);// 抛出客户端错误 T标签翻译
		}
		$hash = $user['password'];
		if(!password_verify($this->password,$hash)) {
			throw new PhalApi_Exception_Error(T('密码错误'), 1);// 抛出客户端错误 T标签翻译
		}
		//将用户名存如SESSION中
		$_SESSION['user_id'] = $user['id'];
		$_SESSION['username'] = $user['username'];
		$_SESSION['user_auth'] = $user['auth'];
		DI()->response->setMsg(T('登陆成功'));
		return;
//		throw new PhalApi_Exception_Error(T('用户名不存在'), 1);// 抛出普通错误 T标签翻译
//		 throw new PhalApi_Exception_BadRequest(T('wrong sign'), 1);// 抛出客户端错误 T标签翻译
//		 throw new PhalApi_Exception_InternalServerError(T('system is busy now'), 2);// 抛出服务端错误
	}

	public function register()
	{
		DI()->view->show('register');
	}

	public function doRegister()
	{
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
}
