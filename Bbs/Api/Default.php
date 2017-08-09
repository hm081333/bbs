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
			'main' => array(
				'page' => array('name' => 'page', 'type' => 'int', 'default' => 1, 'min' => 1, 'desc' => '当前页数'),
			),
			'index' => array(
				'page' => array('name' => 'page', 'type' => 'int', 'default' => 1, 'min' => 1, 'desc' => '当前页数'),
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
		$class_list['page_total'] = ceil($class_list['total'] / each_page);
		DI()->view->show('index', array('rows' => $class_list['rows'], 'total' => $class_list['total'], 'page' => $this->page, 'back' => 0));
	}
	/*public function index()
	{
		DI()->view->show('main');
	}

	public function main()
	{
		$class_domain = new Domain_Class();
		$class_list = $class_domain->getClassList((($this->page - 1) * each_page), ($this->page * each_page));
		$class_list['page_total'] = ceil($class_list['total'] / each_page);
		$html = DI()->view->post('index', array('rows' => $class_list['rows'], 'total' => $class_list['total'], 'page' => $this->page, 'back' => 0));
		$html = Common_Function::higrid_compress_html($html);
		return $html;
	}*/
}
