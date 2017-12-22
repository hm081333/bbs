<?php

class Domain_Test
{

	public static function down()
	{
		$begin = 232791;
		for ($i = 0; $i < 64; $i++) {
			$now = $begin - $i;
			for ($z = 0; $z < 109; $z++) {
				$num = str_pad($z, 4, '0', STR_PAD_LEFT);
				$url = 'http://i.yxniu.com/comic/43/21406/' . $now . '/' . $num . '.jpg';
				$result = DI()->curl->getFile($url, API_ROOT . '/Public/static/download/qigongzhu/' . ($i + 1) . '/', $num . '.jpg');
				$file_size = filesize($result);
				if ($file_size <= 10000) {
					break;
				}
			}
		}
	}

}
