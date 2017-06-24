<?php

/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2017/6/23
 * Time: 下午 10:13
 */
class Plugin_Image implements Wechat_Plugin_Image
{
	public function handleImage($inMessage, &$outMessage)
	{
		Common_Function::getImage($inMessage->getPicUrl(), $inMessage->getMediaId());
		$outMessage = new Wechat_OutMessage_Text();
		$outMessage->setContent('已收到您的图片');
	}
}