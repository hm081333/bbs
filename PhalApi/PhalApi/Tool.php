<?php

/**
 * PhalApi_Tool 工具集合类
 * 只提供通用的工具类操作，目前提供的有：
 * - IP地址获取
 * - 随机字符串生成
 * @package     PhalApi\Tool
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-02-12
 */
class PhalApi_Tool
{

	/**
	 * IP地址获取
	 * @return string 如：192.168.1.1 失败的情况下，返回空
	 */
	public function getClientIp()
	{
		$unknown = 'unknown';
		if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), $unknown)) {
			$ip = getenv('HTTP_CLIENT_IP');
		} else if (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), $unknown)) {
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		} else if (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), $unknown)) {
			$ip = getenv('REMOTE_ADDR');
		} else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown)) {
			$ip = $_SERVER['REMOTE_ADDR'];
		} else {
			$ip = '';
		}

		return $ip;
	}

	/**
	 * 随机字符串生成
	 *
	 * @param int $len 需要随机的长度，不要太长
	 * @param string $chars 随机生成字符串的范围
	 *
	 * @return string
	 */
	public function createRandStr($len, $chars = null)
	{
		if (!$chars) {
			$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		}

		return substr(str_shuffle(str_repeat($chars, rand(5, 8))), 0, $len);
	}

	/**
	 * 获取数组value值不存在时返回默认值
	 * 不建议在大循环中使用会有效率问题
	 *
	 * @param array $arr 数组实例
	 * @param string|int $key 数据key值
	 * @param string $default 默认值
	 *
	 * @return string
	 */
	public function arrIndex($arr, $key, $default = '')
	{

		return isset($arr[$key]) ? $arr[$key] : $default;
	}

	/**
	 * 根据路径创建目录或文件
	 *
	 * @param string $path 需要创建目录路径
	 *
	 * @throws PhalApi_Exception_BadRequest
	 */
	public function createDir($path)
	{

		$dir = explode('/', $path);
		$path = '';
		foreach ($dir as $element) {
			$path .= $element . '/';
			if (!is_dir($path) && !mkdir($path)) {
				throw new PhalApi_Exception_BadRequest(
					T('create file path Error: {filePath}', array('filepath' => $path))
				);
			}
		}
	}

	/**
	 * 删除目录以及子目录等所有文件
	 *
	 * - 请注意不要删除重要目录！
	 *
	 * @param string $path 需要删除目录路径
	 */
	public function deleteDir($path)
	{

		$dir = opendir($path);
		while (false !== ($file = readdir($dir))) {
			if (($file != '.') && ($file != '..')) {
				$full = $path . '/' . $file;
				if (is_dir($full)) {
					$this->deleteDir($full);
				} else {
					unlink($full);
				}
			}
		}
		closedir($dir);
		rmdir($path);
	}

	/**
	 * 数组转XML格式
	 *
	 * @param array $arr 数组
	 * @param string $root 根节点名称
	 * @param int $num 回调次数
	 *
	 * @return string xml
	 */
	public function arrayToXml($arr, $root = 'xml', $num = 0)
	{
		$xml = '';
		if (!$num) {
			$num += 1;
			$xml .= '<?xml version="1.0" encoding="utf-8"?>';
		}
		$xml .= "<$root>";
		foreach ($arr as $key => $val) {
			if (is_array($val)) {
				$xml .= self::arrayToXml($val, "$key", $num);
			} else {
				$xml .= "<" . $key . ">" . $val . "</" . $key . ">";
			}
		}
		$xml .= "</$root>";
		return $xml;
	}

	/**
	 * XML格式转数组
	 *
	 * @param  string $xml
	 *
	 * @return mixed|array
	 */
	public function xmlToArray($xml)
	{
		//禁止引用外部xml实体
		libxml_disable_entity_loader(true);
		$xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
		$arr = json_decode(json_encode($xmlstring), true);
		return $arr;
	}

	/**
	 * 去除字符串空格和回车
	 *
	 * @param  string $str 待处理字符串
	 *
	 * @return string
	 */
	public function trimSpaceInStr($str)
	{
		$pat = array(" ", "　", "\t", "\n", "\r");
		$string = array("", "", "", "", "",);
		return str_replace($pat, $string, $str);
	}

	/**
	 * @param string $data
	 * @return string
	 */
	public function decode($data)
	{
		$privateKey = '@fdskalhfj2387A!';
		$iv = '@fdfpu+adj2387A!';
		$encryptedData = base64_decode($data);
		$decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $privateKey, $encryptedData, MCRYPT_MODE_CBC, $iv);
		$decrypted = rtrim($decrypted, "\0");//解密出来的数据后面会出现如图所示的六个红点；这句代码可以处理掉，从而不影响进一步的数据操作
		return $decrypted;
	}

	/**
	 * @param string $data
	 * @return string
	 */
	public function encode($data)
	{
		$privateKey = '@fdskalhfj2387A!';
		$iv = '@fdfpu+adj2387A!';
		$encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $privateKey, $data, MCRYPT_MODE_CBC, $iv);
		$encode = base64_encode($encrypted);
		return $encode;
	}

	public function is_mobile_request()
	{
		$_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';
		$mobile_browser = '0';
		if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT'])))
			$mobile_browser++;
		if ((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/vnd.wap.xhtml+xml') !== false))
			$mobile_browser++;
		if (isset($_SERVER['HTTP_X_WAP_PROFILE']))
			$mobile_browser++;
		if (isset($_SERVER['HTTP_PROFILE']))
			$mobile_browser++;
		$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
		$mobile_agents = array(
			'w3c ', 'acs-', 'alav', 'alca', 'amoi', 'audi', 'avan', 'benq', 'bird', 'blac',
			'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric', 'hipt', 'inno',
			'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c', 'lg-d', 'lg-g', 'lge-',
			'maui', 'maxo', 'midp', 'mits', 'mmef', 'mobi', 'mot-', 'moto', 'mwbp', 'nec-',
			'newt', 'noki', 'oper', 'palm', 'pana', 'pant', 'phil', 'play', 'port', 'prox',
			'qwap', 'sage', 'sams', 'sany', 'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar',
			'sie-', 'siem', 'smal', 'smar', 'sony', 'sph-', 'symb', 't-mo', 'teli', 'tim-',
			'tosh', 'tsm-', 'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp',
			'wapr', 'webc', 'winw', 'winw', 'xda', 'xda-'
		);
		if (in_array($mobile_ua, $mobile_agents))
			$mobile_browser++;
		if (strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false)
			$mobile_browser++;
		// Pre-final check to reset everything if the user is on Windows
		if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false)
			$mobile_browser = 0;
		// But WP7 is also Windows, with a slightly different characteristic
		if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false)
			$mobile_browser++;
		if ($mobile_browser > 0)
			return true;
		else
			return false;
	}

	/**
	 * PHP压缩html js css的函数
	 * 激进
	 * 清除换行符,清除制表符,去掉注释标记
	 *
	 * 网页里面的js代码中不要使用//行注释
	 * /* 块注释会自动剔除
	 *
	 * 函数自动剔除标记之间多余的空白
	 *
	 * 判断标记的属性的属性值是否被""包裹之间
	 * 如果有就剔除属性和属性值之间的所有空格
	 * 如果没有""就保留一个空格，避免破坏html结构。
	 *
	 * @param $string string HTML内容
	 * @return $string 压缩后HTML内容
	 */
	public function compress_html($string)
	{
		$string = str_replace("\r\n", '', $string); //清除换行符
		$string = str_replace("\n", '', $string); //清除换行符
		$string = str_replace("\t", '', $string); //清除制表符
		$pattern = array(//去掉注释标记
			"/> *([^ ]*) *</",
			"/[\s]+/",
			"/<!--[\\w\\W\r\\n]*?-->/",
			"/\" /",
			"/ \"/",
			"'/\*[^*]*\*/'"
		);
		$replace = array(
			">\\1<",
			" ",
			"",
			"\"",
			"\"",
			""
		);
		return preg_replace($pattern, $replace, $string);
	}

	/**
	 * higrid.net 的 php压缩html函数
	 * 没看懂
	 * @param $higrid_uncompress_html_source
	 * @return string
	 */
	public function higrid_compress_html($higrid_uncompress_html_source)
	{
		$chunks = preg_split('/(<pre.*?\/pre>)/ms', $higrid_uncompress_html_source, -1, PREG_SPLIT_DELIM_CAPTURE);
		$higrid_uncompress_html_source = '';//[higrid.net]修改压缩html : 清除换行符,清除制表符,去掉注释标记
		foreach ($chunks as $c) {
			if (strpos($c, '<pre') !== 0) {
				//[higrid.net] remove new lines & tabs
				$c = preg_replace('/[\\n\\r\\t]+/', ' ', $c);
				// [higrid.net] remove extra whitespace
				$c = preg_replace('/\\s{2,}/', ' ', $c);
				// [higrid.net] remove inter-tag whitespace
				$c = preg_replace('/>\\s</', '><', $c);
				// [higrid.net] remove CSS & JS comments
				$c = preg_replace('/\\/\\*.*?\\*\\//i', '', $c);
			}
			$higrid_uncompress_html_source .= $c;
		}
		return $higrid_uncompress_html_source;
	}


}