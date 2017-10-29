<?php

/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2017/6/23
 * Time: 下午 10:13
 */
class Plugin_Voice implements Wechat_Plugin_Voice
{
	public function handleVoice($inMessage, &$outMessage)
	{
		$ask = Common_Function::tuling($inMessage->getRecognition());
		if ($ask['code'] == '100000') {
			$outMessage = new Wechat_OutMessage_Text();
			$outMessage->setContent($ask['text']);
		} elseif (($ask['code'] == '200000' || $ask['code'] == '302000') && empty($ask['list'])) {
			$outMessage = new Wechat_OutMessage_News();
			$item = new Wechat_OutMessage_News_Item();
			$item->setTitle($inMessage->getContent())
				->setDescription($ask['text'])
				//->setPicUrl($ask['url'])
				->setUrl($ask['url']);
			$outMessage->addItem($item);
		} else {
			file_put_contents(API_ROOT . '/Config/test.php', "<?php   \nreturn " . var_export($ask, true) . ';');
			$outMessage = new Wechat_OutMessage_News();
			foreach ($ask['list'] as $key => $rs) {
				if ($key >= 8) {
					break;
				}
				$item = new Wechat_OutMessage_News_Item();
				$item->setTitle($rs['name'])
					->setDescription($rs['info'])
					->setPicUrl($rs['icon'])
					->setUrl($rs['detailurl']);
				$outMessage->addItem($item);
			}
		}
	}
}