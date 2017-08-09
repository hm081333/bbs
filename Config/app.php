<?php
/**
 * 请在下面放置任何您需要的应用配置
 */

return array(

	/**
	 * 应用接口层的统一参数
	 */
	'apiCommonRules' => array(//'sign' => array('name' => 'sign', 'require' => true),
	),

	/**
	 * 接口服务白名单，格式：接口服务类名.接口服务方法名
	 *
	 * 示例：
	 * - *.*            通配，全部接口服务，慎用！
	 * - Default.*      Api_Default接口类的全部方法
	 * - *.Index        全部接口类的Index方法
	 * - Default.Index  指定某个接口服务，即Api_Default::Index()
	 */
	/*'service_whitelist' => array(
		'Default.Index',
	),*/

	'Wechat' => array(
		'plugins' => array(
			Wechat_InMessage::MSG_TYPE_TEXT => array('Plugin_Text',),
			Wechat_InMessage::MSG_TYPE_IMAGE => array('Plugin_Image',),
			Wechat_InMessage::MSG_TYPE_VOICE => array('Plugin_Voice',),
			Wechat_InMessage::MSG_TYPE_VIDEO => array('Plugin_Video',),
			Wechat_InMessage::MSG_TYPE_LOCATION => array('Plugin_Location',),
			Wechat_InMessage::MSG_TYPE_LINK => array('Plugin_Link',),
			Wechat_InMessage::MSG_TYPE_EVENT => array('Plugin_Event',),
			Wechat_InMessage::MSG_TYPE_DEVICE_EVENT => array('Plugin_DeviceEvent',),
			Wechat_InMessage::MSG_TYPE_DEVICE_TEXT => array('Plugin_DeviceText',),
		),
		'config' => array(
			'appID' => 'wx10cfa95954e03f6b',
			'appsecret' => '3247476d1834940ddf6e11739b48e2c6',
		)
	),

	'tuling_config' => array(
		'APIkey' => '7fec48a1322b4e06827f600e04fd080f',
		'URI'=>'http://www.tuling123.com/openapi/api'
	),

	'baidu_map_config' => array(
		'ak' => 'OO9EUBzBqEqGnA9ItB62uUL1AaWc5n4l',
		'sk'=>'6g6g5oDczqelrOwR921ntPekrDFQGcC3'
	)
);
