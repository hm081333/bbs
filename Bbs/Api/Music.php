<?php

class Api_Music extends PhalApi_Api
{
	public function getRules()
	{
		return array(
			'index' => array(//'page' => array('name' => 'page', 'type' => 'int', 'default' => 1, 'min' => 1, 'desc' => '当前页数'),
			),
		);
	}

	public function index()
	{
		DI()->view->show('music', array('back' => 0));
	}


}
