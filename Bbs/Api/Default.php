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
				// 'limit' => array('name' => 'limit', 'type' => 'int', 'default' => 0, 'min' => 0, 'require' => false, 'desc' => ''),
				// 'offset' => array('name' => 'offset', 'type' => 'int', 'default' => each_page, 'min' => 0, 'require' => false, 'desc' => ''),
				'page' => array('name' => 'page', 'type' => 'int', 'default' => 1, 'min' => 1, 'require' => false, 'desc' => '当前页数'),
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
		$class_domain = new Domain_Class();
		$class_list = $class_domain->getClassList((($this->page - 1) * each_page), ($this->page * each_page));
//		var_dump($class_list);

		//抛出变量
//		DI()->view->assign($xx);

		//抛出多个变量
		DI()->view->assign(array('rows' => $class_list['rows'], 'total' => $class_list['total'], 'page' => $this->page, 'back' => 0));

		//引入模板
		DI()->view->show('index');
		// throw new PhalApi_Exception_BadRequest(T('wrong sign'), 1);// 抛出客户端错误 T标签翻译
		// throw new PhalApi_Exception_InternalServerError(T('system is busy now'), 2);// 抛出服务端错误
	}
}
