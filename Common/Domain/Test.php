<?php

class Domain_Test
{

	public static function down()
	{
		$result = DI()->curl->getFile('http://i.yxniu.com/comic/43/21406/232791/0001.jpg', API_ROOT . '/Public/static/download/qigongzhu/1/', '0001.jpg');
		var_dump($result);
		die;
	}
}
