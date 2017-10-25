<?php

class Api_Default extends PhalApi_Api
{

	public function getRules()
	{
		return array(
			'index' => array(
				'page' => array('name' => 'page', 'type' => 'int', 'default' => 1, 'min' => 1, 'require' => false, 'desc' => '当前页数'),
			),
		);
	}

	public function index()
	{
		if (isset($_SESSION["user_id"])) {
			$baiduid_model = new Model_BaiduId();
			$user_id = $_SESSION['user_id'];
			$bduss_list = $baiduid_model->getList((($this->page - 1) * each_page), ($this->page * each_page), array('user_id' => $user_id), '*', 'id asc');
			//抛出多个变量
			DI()->view->assign(array('rows' => $bduss_list['rows'], 'total' => $bduss_list['total'], 'page' => $this->page));
			DI()->view->show('bduss_list');
			return;
		} else {
			DI()->view->show('login');
			return;
		}
	}

}
