<?php

/**
 * 默认接口服务类
 *
 * @author: dogstar <chanzonghuang@gmail.com> 2014-10-04
 */
class Api_Reply extends PhalApi_Api
{

	public function getRules()
	{
		return array(
			'add_Reply' => array(
				'topic_id' => array('name' => 'topic_id', 'type' => 'int', 'require' => true, 'desc' => 'ID'),
				'user_id' => array('name' => 'user_id', 'type' => 'int', 'require' => true, 'desc' => '用户ID'),
				'reply_detail' => array('name' => 'reply_detail', 'type' => 'string', 'require' => true, 'desc' => '回复内容')
			)
		);
	}

	public function add_Reply()
	{
		if (empty($this->user_id)) {
			throw new PhalApi_Exception_Error(T('请登陆后回复'), 1);// 抛出普通错误 T标签翻译
		} elseif (empty($this->reply_detail)) {
			throw new PhalApi_Exception_Error(T('请输入内容'), 1);// 抛出普通错误 T标签翻译
		}
		$reply_domain = new Domain_Reply();
		$rs = $reply_domain->add_Reply($this->topic_id, $this->user_id, $this->reply_detail);
		if ($rs) {
			DI()->response->setMsg(T('回复成功'));
			return;
		} else {
			throw new PhalApi_Exception_InternalServerError(T('回复失败'), 2);// 抛出服务端错误
		}
	}

}
