<?php
/**
 * Created by PhpStorm.
 * User: 123
 * Date: 2017/9/8
 * Time: 21:36
 */

class Api_System extends PhalApi_Api
{
	public function getRules()
	{
		return array(
			'restore' => array(
				'action' => array('name' => 'action', 'type' => 'enum', 'range' => array('view', 'post'), 'default' => 'view', 'desc' => '操作类型'),
				'password' => array('name' => 'password', 'type' => 'string', 'desc' => '密码'),
			),
		);
	}

	public function restore()
	{
		if ($this->action == 'view') {
			DI()->view->show('system_restore');
		} else {
			if (empty($this->password)) {
				throw new PhalApi_Exception_Error('请输入管理员密码');
			}
			$admin_model = new Model_Admin();
			$admin = $admin_model->get(1, 'password');
			if (!Domain_Common::verify($this->password, $admin['password'])) {
				throw new PhalApi_Exception_Error(T('密码错误'), 1);// 抛出客户端错误 T标签翻译
			}
			$sql = '
			truncate table forum_user;
			truncate table forum_topic;
			truncate table forum_reply;
			truncate table forum_email_auth;
			truncate table forum_delivery;
			';
			$admin_model->queryAll($sql);
			DI()->tool->emptyDir(PUB_ROOT . 'static/upload/pics');
			DI()->tool->emptyDir(PUB_ROOT . 'static/upload/wechat');
			DI()->response->setMsg(T('清空成功'));
			return;
		}
	}

}