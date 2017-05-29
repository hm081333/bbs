<?php

/**
 * 默认接口服务类
 *
 * @author: dogstar <chanzonghuang@gmail.com> 2014-10-04
 */
class Api_Topic extends PhalApi_Api
{

	public function getRules()
	{
		return array(
			'topic_List' => array(
				 'class_id' => array('name' => 'class_id', 'type' => 'int', 'default' => 0, 'min' => 0, 'require' => true, 'desc' => 'ID'),
				// 'offset' => array('name' => 'offset', 'type' => 'int', 'default' => each_page, 'min' => 0, 'require' => false, 'desc' => ''),
				'page' => array('name' => 'page', 'type' => 'int', 'default' => 1, 'min' => 1, 'require' => false, 'desc' => '当前页数'),
			),
			'topic' => array(
				'topic_id' => array('name' => 'topic_id', 'type' => 'int', 'default' => 0, 'min' => 0, 'require' => true, 'desc' => 'ID'),
//				 'offset' => array('name' => 'offset', 'type' => 'int', 'default' => each_page, 'min' => 0, 'require' => false, 'desc' => ''),
//				'page' => array('name' => 'page', 'type' => 'int', 'default' => 1, 'min' => 1, 'require' => false, 'desc' => '当前页数'),
			),
		);
	}

	public function topic_List()
	{
		$topic_domain = new Domain_Topic();
		$topic_list = $topic_domain->getTopicList((($this->page - 1) * each_page), ($this->page * each_page), array('class_id' => $this->class_id));
//		var_dump($class_list);

		//抛出变量
//		DI()->view->assign($xx);

		//抛出多个变量
		DI()->view->assign(array('page' => $this->page));
		DI()->view->assign(array('total' => $topic_list['total'], 'rows' => $topic_list['rows']));
		DI()->view->assign(array('class' => $topic_list['class']));

		//引入模板
		DI()->view->show('topic_list');
		// throw new PhalApi_Exception_BadRequest(T('wrong sign'), 1);// 抛出客户端错误 T标签翻译
		// throw new PhalApi_Exception_InternalServerError(T('system is busy now'), 2);// 抛出服务端错误
	}

	public function topic()
	{
		$topic_model = new Model_Topic();
		$topic = $topic_model->get($this->topic_id);
		$topic_model->update($this->topic_id, array('view' => new NotORM_Literal("view + 1")));//浏览量加1
		$reply_domain = new Domain_Reply();
		$reply_list = $reply_domain->getReplyList(array('topic_id' => $this->topic_id));
//		var_dump($reply_list);

		//抛出变量
//		DI()->view->assign($xx);

		//抛出多个变量
//		DI()->view->assign(array('page' => $this->page));
//		DI()->view->assign(array('total' => $topic_list['index'], 'rows' => $topic_list['rows']));
		DI()->view->assign(array('topic' => $topic, 'reply' => $reply_list));

		//引入模板
		DI()->view->show('view_topic');
		// throw new PhalApi_Exception_BadRequest(T('wrong sign'), 1);// 抛出客户端错误 T标签翻译
		// throw new PhalApi_Exception_InternalServerError(T('system is busy now'), 2);// 抛出服务端错误
	}
}
