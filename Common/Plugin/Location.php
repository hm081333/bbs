<?php

/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2017/6/23
 * Time: 下午 10:13
 */
class Plugin_Location implements Wechat_Plugin_Location
{
	public function handleLocation($inMessage, &$outMessage)
	{
		//file_put_contents(API_ROOT . '/Config/test.php', "<?php   \nreturn " . var_export($inMessage, true) . ';');
		$Location_X = $inMessage->getLocation_X();
		$Location_Y = $inMessage->getLocation_Y();
		//$Scale = $inMessage->getScale();
		$rs = Common_Function::baidu_map($Location_X . ',' . $Location_Y, 'location');
		$outMessage = new Wechat_OutMessage_Text();
		$outMessage->setContent('定位' . $rs['result']['formatted_address']);
	}
}