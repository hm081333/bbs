<?php

/**
 * 默认接口服务类
 *
 * @author: dogstar <chanzonghuang@gmail.com> 2014-10-04
 */
class Api_Default extends PhalApi_Api
{

	public function getRules()
	{
		return array(
			'index' => array(
				'page' => array('name' => 'page', 'type' => 'int', 'default' => 1, 'min' => 1, 'require' => false, 'desc' => '当前页数'),
			),
			'email_config' => array(
				'action' => array('name' => 'action', 'type' => 'string', 'default' => 'view', 'require' => true, 'desc' => '操作'),
				'host' => array('name' => 'host', 'type' => 'string', 'require' => false, 'desc' => 'SMTP服务器'),
				'username' => array('name' => 'username', 'type' => 'string', 'require' => false, 'desc' => 'SMTP服务器用户名'),
				'password' => array('name' => 'password', 'type' => 'string', 'require' => false, 'desc' => 'SMTP服务器密码'),
				'from' => array('name' => 'from', 'type' => 'string', 'require' => false, 'desc' => '发件人地址'),
				'fromName' => array('name' => 'fromName', 'type' => 'string', 'require' => false, 'desc' => '发件人名称'),
				'sign' => array('name' => 'sign', 'type' => 'string', 'require' => false, 'desc' => '邮件签名'),
			),
			'smsbao_config' => array(
				'action' => array('name' => 'action', 'type' => 'string', 'default' => 'view', 'require' => true, 'desc' => '操作'),
				'username' => array('name' => 'username', 'type' => 'string', 'require' => false, 'desc' => '用户名'),
				'password' => array('name' => 'password', 'type' => 'string', 'require' => false, 'desc' => '密码'),
			),
		);
	}

	/**
	 * 默认接口服务
	 * @return string title 标题
	 * @return string content 内容
	 * @return string version 版本，格式：X.X.X
	 * @return int time 当前时间戳
	 */
	public function index()
	{
		if (isset($_SESSION["user_id"])) {
			$baiduid_model = new Model_BaiduId();
			$bduss_list = $baiduid_model->getList((($this->page - 1) * each_page), ($this->page * each_page), array(), '*', 'id asc');
			//抛出多个变量
			DI()->view->assign(array('rows' => $bduss_list['rows'], 'total' => $bduss_list['total'], 'page' => $this->page));
			DI()->view->show('bduss_list');
		} else {
			DI()->view->show('login');
		}
	}

	public function email_config()
	{
		if ($this->action == 'post') {
			if (empty($this->host)) {
				throw new PhalApi_Exception_Error(T('请输入SMTP服务器'), 1);// 抛出普通错误 T标签翻译
			} elseif (empty($this->username)) {
				throw new PhalApi_Exception_Error(T('请输入SMTP服务器用户名'), 1);// 抛出普通错误 T标签翻译
			} elseif (empty($this->password)) {
				throw new PhalApi_Exception_Error(T('请输入SMTP服务器密码'), 1);// 抛出普通错误 T标签翻译
			} elseif (empty($this->from)) {
				throw new PhalApi_Exception_Error(T('请输入发件人地址'), 1);// 抛出普通错误 T标签翻译
			} elseif (empty($this->fromName)) {
				throw new PhalApi_Exception_Error(T('请输入发件人名称'), 1);// 抛出普通错误 T标签翻译
			} elseif (empty($this->sign)) {
				throw new PhalApi_Exception_Error(T('请输入邮件签名'), 1);// 抛出普通错误 T标签翻译
			}
			$config = array();
			$config['email']['host'] = $this->host;
			$config['email']['username'] = $this->username;
			$config['email']['password'] = $this->password;
			$config['email']['from'] = $this->from;
			$config['email']['fromName'] = $this->fromName;
			$config['email']['sign'] = $this->sign;
			file_put_contents(API_ROOT . '/Config/email.php', "<?php   \nreturn " . var_export($config, true) . ';');
			unset($config);
			DI()->response->setMsg(T('设置成功'));
		} else {
			$email_config = DI()->config->get('email.email');
			DI()->view->assign(array('email_config' => $email_config));
			DI()->view->show('email_config');
		}
	}

	public function smsbao_config()
	{
		if ($this->action == 'post') {
			if (empty($this->username)) {
				throw new PhalApi_Exception_Error(T('请输入用户名'), 1);// 抛出普通错误 T标签翻译
			} elseif (empty($this->password)) {
				throw new PhalApi_Exception_Error(T('请输入密码'), 1);// 抛出普通错误 T标签翻译
			}
			$rs = Domain_Common::query_smsbao($this->username, $this->password);
			if ($rs[0] != 0) {
				throw new PhalApi_Exception_Error(T($rs['msg']), 1);// 抛出普通错误 T标签翻译
			}
			$config = array();
			$config['smsbao']['username'] = $this->username;
			$config['smsbao']['password'] = $this->password;
			file_put_contents(API_ROOT . '/Config/sms.php', "<?php   \nreturn " . var_export($config, true) . ';');
			unset($config);
			DI()->response->setMsg(T('设置成功'));
		} else {
			$smsbao_config = DI()->config->get('sms.smsbao');
			if (!empty($smsbao_config)) {
				$rs = Domain_Common::query_smsbao();
				DI()->view->assign(array('smsbao' => $smsbao_config, 'smsbao_query' => $rs));
			}
			DI()->view->show('smsbao_config');
		}
	}

}
