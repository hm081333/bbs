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
				'user_name' => array('name' => 'user_name', 'type' => 'string', 'require' => true, 'desc' => '用户名'),
				'password' => array('name' => 'password', 'type' => 'string', 'require' => true, 'desc' => '用户密码'),
			),
			'register' => array(
				'action' => array('name' => 'action', 'type' => 'string', 'default' => 'view', 'require' => true, 'desc' => '操作'),
				'user_name' => array('name' => 'user_name', 'type' => 'string', 'require' => false, 'desc' => '用户名'),
				'password' => array('name' => 'password', 'type' => 'string', 'require' => false, 'desc' => '用户密码'),
				'email' => array('name' => 'email', 'type' => 'string', 'require' => false, 'desc' => '用户邮箱'),
				'real_name' => array('name' => 'real_name', 'type' => 'string', 'require' => false, 'desc' => '用户姓名'),
				'auth' => array('name' => 'auth', 'type' => 'string', 'require' => false, 'desc' => '用户权限')
			),
			'create_admin' => array(
				'action' => array('name' => 'action', 'type' => 'string', 'default' => 'view', 'require' => true, 'desc' => '操作'),
				'user_name' => array('name' => 'user_name', 'type' => 'string', 'require' => false, 'desc' => '用户名'),
				'password' => array('name' => 'password', 'type' => 'string', 'require' => false, 'desc' => '用户密码'),
				'auth' => array('name' => 'auth', 'type' => 'string', 'require' => false, 'desc' => '用户权限')
			),
			'admin_list' => array(
				'action' => array('name' => 'action', 'type' => 'string', 'default' => 'view', 'require' => true, 'desc' => '操作'),
				'page' => array('name' => 'page', 'type' => 'int', 'default' => 1, 'min' => 1, 'require' => false, 'desc' => '当前页数'),
				'admin_id' => array('name' => 'admin_id', 'type' => 'int', 'require' => false, 'desc' => '用户ID'),
				'password' => array('name' => 'password', 'type' => 'string', 'require' => false, 'desc' => '用户密码'),
				'auth' => array('name' => 'auth', 'type' => 'string', 'require' => false, 'desc' => '用户权限')
			),
			'delete_Admin' => array(
				'admin_id' => array('name' => 'admin_id', 'type' => 'int', 'require' => true, 'desc' => '用户ID')
			),
			'user_Info' => array(
				'user_id' => array('name' => 'user_id', 'type' => 'int', 'require' => true, 'desc' => '用户ID')
			),
			'edit_Member' => array(
				'action' => array('name' => 'action', 'type' => 'string', 'default' => 'view', 'require' => true, 'desc' => '操作'),
				'user_id' => array('name' => 'user_id', 'type' => 'int', 'require' => true, 'desc' => '用户ID'),
				'password' => array('name' => 'password', 'type' => 'string', 'require' => false, 'desc' => '用户密码'),
				'email' => array('name' => 'email', 'type' => 'string', 'require' => false, 'desc' => '用户邮箱'),
				'real_name' => array('name' => 'real_name', 'type' => 'string', 'require' => false, 'desc' => '用户姓名'),
				'auth' => array('name' => 'auth', 'type' => 'string', 'require' => false, 'desc' => '用户权限')
			),
			'delete_User' => array(
				'user_id' => array('name' => 'user_id', 'type' => 'int', 'require' => true, 'desc' => '用户ID')
			),
		);
	}

	public function login()
	{
		if ($this->action == 'post') {
			$admin_model = new Model_Admin();
			if (empty($this->user_name)) {
				throw new PhalApi_Exception_Error(T('请输入用户名'), 1);// 抛出普通错误 T标签翻译
			} elseif (empty($this->password)) {
				throw new PhalApi_Exception_Error(T('请输入密码'), 1);// 抛出普通错误 T标签翻译
			}
			$admin = $admin_model->getInfo(array('user_name' => $this->user_name), '*');
			if ($admin === false) {
				throw new PhalApi_Exception_Error(T('用户名不存在'), 1);// 抛出客户端错误 T标签翻译
			} elseif (!Domain_Common::verify($this->password, $admin['password'])) {
				throw new PhalApi_Exception_Error(T('密码错误'), 1);// 抛出客户端错误 T标签翻译
			} else {
				//将用户名存如SESSION中
				DI()->cookie->set(ADMIN_TOKEN, serialize($admin));
				$_SESSION['admin_id'] = $admin['id'];
				$_SESSION['admin_name'] = $admin['user_name'];
				$_SESSION['admin_auth'] = $admin['auth'];
				DI()->response->setMsg(T('登陆成功'));
				return 'admin';
			}
		} else {
			DI()->view->show('login');
		}
	}

	public function register()
	{
		if ($this->action == 'post') {
			$user_model = new Model_User();
			if (empty($this->user_name)) {
				throw new PhalApi_Exception_Error(T('请输入用户名'), 1);// 抛出普通错误 T标签翻译
			} elseif (empty($this->password)) {
				throw new PhalApi_Exception_Error(T('请输入密码'), 1);// 抛出普通错误 T标签翻译
			} elseif (empty($this->email)) {
				throw new PhalApi_Exception_Error(T('请输入邮箱'), 1);// 抛出普通错误 T标签翻译
			} elseif (empty($this->real_name)) {
				throw new PhalApi_Exception_Error(T('请输入姓名'), 1);// 抛出普通错误 T标签翻译
			}
			$user = $user_model->getInfo(array('user_name' => $this->user_name), 'id');
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

			$insert_data = array();
			$insert_data['user_name'] = $this->user_name;
			$insert_data['password'] = Domain_Common::hash($this->password);
			$insert_data['a_pwd'] = DI()->tool->encrypt($this->password);
			$insert_data['email'] = $this->email;
			$insert_data['real_name'] = $this->real_name;
			if ($this->auth == 'on') {
				$insert_data['auth'] = 1;
			}
			$rs = $user_model->insert($insert_data);
			unset($insert_data);
			if ($rs) {
				$_SESSION['user_id'] = $rs;
				$_SESSION['user_name'] = $this->user_name;
				$_SESSION['user_auth'] = 0;
				DI()->response->setMsg(T('新增用户成功'));
				return 'admin';
			} else {
				throw new PhalApi_Exception_InternalServerError(T('新增用户失败'), 2);// 抛出服务端错误
			}
		} else {
			DI()->view->show('create_user');
		}
	}

	public function create_admin()
	{
		if ($this->action == 'post') {
			$admin_model = new Model_Admin();
			if (empty($this->user_name)) {
				throw new PhalApi_Exception_Error(T('请输入用户名'), 1);// 抛出普通错误 T标签翻译
			} elseif (empty($this->password)) {
				throw new PhalApi_Exception_Error(T('请输入密码'), 1);// 抛出普通错误 T标签翻译
			}
			$user = $admin_model->getInfo(array('user_name' => $this->user_name), 'id');
			if ($user !== false) {
				throw new PhalApi_Exception_Error(T('用户名已注册'), 1);// 抛出普通错误 T标签翻译
			} elseif (strlen($this->password) < 6) {
				throw new PhalApi_Exception_Error(T('请输入6位长度密码'), 1);// 抛出普通错误 T标签翻译
			}

			$insert_data = array();
			$insert_data['user_name'] = $this->user_name;
			$insert_data['password'] = Domain_Common::hash($this->password);
			if ($this->auth == 'on') {
				$insert_data['auth'] = 1;
			}
			$rs = $admin_model->insert($insert_data);
			unset($insert_data);
			if ($rs) {
				$_SESSION['user_id'] = $rs;
				$_SESSION['user_name'] = $this->user_name;
				$_SESSION['user_auth'] = 0;
				DI()->response->setMsg(T('新增管理员成功'));
				return;
			} else {
				throw new PhalApi_Exception_InternalServerError(T('新增管理员失败'), 2);// 抛出服务端错误
			}
		} else {
			DI()->view->show('create_admin');
		}
	}

	public function admin_list()
	{
		if ($this->action == 'post') {
			$admin_domain = new Domain_Admin();
			$rs = $admin_domain->edit_Admin_Member($this->admin_id, $this->password, $this->auth);
			DI()->response->setMsg(T('修改成功'));
			return;
		} else {
			$admin_domain = new Domain_Admin();
			$admin_list = $admin_domain->getAdminList((($this->page - 1) * each_page), ($this->page * each_page));
			//抛出多个变量
			DI()->view->assign(array('rows' => $admin_list['rows'], 'total' => $admin_list['total'], 'page' => $this->page));
			DI()->view->show('admin_list');
		}
	}

	public function delete_Admin()
	{
		$admin_model = new Model_Admin();
		$admin = $admin_model->get($this->admin_id);
		if (!$admin) {
			throw new PhalApi_Exception_Error(T('管理员不存在'), 1);// 抛出普通错误 T标签翻译
		}
		$rs = $admin_model->delete($admin['id']);
		if ($rs === 'false') {
			throw new PhalApi_Exception_InternalServerError(T('删除失败'), 2);// 抛出服务端错误
		}
		DI()->response->setMsg(T('删除成功'));
		return;
	}

	public function logoff()
	{
		/*$_SESSION = array();
		session_unset();
		//清空SESSION
		session_destroy();*/
		//仅清除管理员相关session
		if (isset($_SESSION['admin_id'])) {
			unset($_SESSION['admin_id']);
		}
		if (isset($_SESSION['admin_name'])) {
			unset($_SESSION['admin_name']);
		}
		if (isset($_SESSION['admin_auth'])) {
			unset($_SESSION['admin_auth']);
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
			$rs = $user_domain->edit_Member($this->user_id, $this->password, $this->email, $this->real_name, $this->auth, true);
			DI()->response->setMsg(T('修改成功'));
			return;
		} else {
			$user_domain = new Domain_User();
			$user = $user_domain->userInfo($this->user_id);
			DI()->view->assign(array('user' => $user['info']));
			DI()->view->show('edit_member');
		}
	}

	public function delete_User()
	{
		$user_model = new Model_User();
		$user = $user_model->get($this->user_id);
		if (!$user) {
			throw new PhalApi_Exception_Error(T('会员不存在'), 1);// 抛出普通错误 T标签翻译
		}
		$rs = $user_model->delete($user['id']);
		if ($rs === 'false') {
			throw new PhalApi_Exception_InternalServerError(T('删除失败'), 2);// 抛出服务端错误
		}
		DI()->response->setMsg(T('删除成功'));
		return;
	}


}
