<?php

/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2017/6/23
 * Time: 下午 11:49
 */
class Common_Function
{

	/**
	 * 压缩HTML字符串
	 * @param $string
	 * @return string
	 */
	public static function compress_html($string)
	{
		return ltrim(rtrim(preg_replace(array("/> *([^ ]*) *</", "//", "'/\*[^*]*\*/'", "/\r\n/", "/\n/", "/\t/", '/>[ ]+</'), array(">\\1<", '', '', '', '', '', '><'), $string)));
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
	 * @param $string HTML内容
	 * @return $string 压缩后HTML内容
	 */
	public static function compress_html1($string)
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
	public static function higrid_compress_html($higrid_uncompress_html_source)
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

	/**
	 * @param $question 问题
	 * @return array
	 */
	public static function tuling($question)
	{
		$tuling_config = DI()->config->get('app.tuling_config');
		$rs = self::curl_request($tuling_config['URI'], array('key' => $tuling_config['APIkey'], 'info' => $question));
		return $rs;
	}

	public static function baidu_map($data, $data_type = 'location')
	{
		$baidu_map_config = DI()->config->get('app.baidu_map_config');
		//API控制台申请得到的ak（此处ak值仅供验证参考使用）
		$ak = $baidu_map_config['ak'];
		//应用类型为for server, 请求校验方式为sn校验方式时，系统会自动生成sk，可以在应用配置-设置中选择Security Key显示进行查看（此处sk值仅供验证参考使用）
		$sk = $baidu_map_config['sk'];
		//以Geocoding服务为例，地理编码的请求url，参数待填
		if ($data_type == 'address') {
			$url = "http://api.map.baidu.com/geocoder/v2/?address=%s&output=%s&ak=%s&sn=%s";
		} elseif ($data_type == 'location') {
			$url = 'http://api.map.baidu.com/geocoder/v2/?location=%s&output=%s&ak=%s&sn=%s';
		}
		//get请求uri前缀
		$uri = '/geocoder/v2/';
		//地理编码的请求output参数
		$output = 'json';
		//构造请求串数组
		$param = array(
			$data_type => $data,
			'output' => $output,
			'ak' => $ak
		);
		//调用sn计算函数，默认get请求
		$sn = self::caculateAKSN($ak, $sk, $uri, $param);
		//请求参数中有中文、特殊字符等需要进行urlencode，确保请求串与sn对应
		$request = sprintf($url, urlencode($data), $output, $ak, $sn);
		//执行请求的url
		$rs = self::curl_request($request);
		if ($rs['status'] !== 0) {
			DI()->logger->error('百毒地图API报错，错误码：' . $rs['status'] . '，错误信息：' . $rs['message']);
		}
		return $rs;
	}

	public static function caculateAKSN($ak, $sk, $url, $param, $method = 'GET')
	{
		if ($method === 'POST') {
			ksort($param);
		}
		$querystring = http_build_query($param);
		return md5(urlencode($url . '?' . $querystring . $sk));
	}

	/**
	 * @param $url 访问的URL
	 * @param array $post post数据(不填则为GET)
	 * @param bool $returnJson 是否返回json（默认返回数组）
	 * @param string $cookie 提交的$cookies
	 * @param bool $returnCookie 是否返回$cookies
	 * @return mixed|string
	 */
	public static function curl_request($url, $post = false, $returnJson = false, $https = false, $cookie = false, $returnCookie = false)
	{
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		$user_agent ? $user_agent : 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)';
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_USERAGENT, $user_agent);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_AUTOREFERER, true);
		//伪造来源referer
		//curl_setopt($curl, CURLOPT_REFERER, $referer);
		//伪造来源ip
		//curl_setopt($curl, CURLOPT_HTTPHEADER, $header_ip);
		if ($post) {
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
		}
		if ($https) {
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);                // 对认证证书来源的检查
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);                // 从证书中检查SSL加密算法是否存在
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);                // 使用自动跳转
		}
		if ($cookie) {
			curl_setopt($curl, CURLOPT_COOKIE, $cookie);
		}
		curl_setopt($curl, CURLOPT_HEADER, $returnCookie);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($curl);
		if (curl_errno($curl)) {
			return curl_error($curl);
		}
		curl_close($curl);
		if ($returnJson == false) {
			$data = json_decode($data, true);//将json解码
		}
		if ($returnCookie) {
			list($header, $body) = explode("\r\n\r\n", $data, 2);
			preg_match_all("/Set\-Cookie:([^;]*);/", $header, $matches);
			$info['cookie'] = substr($matches[1][0], 1);
			$info['content'] = $body;
			return $info;
		} else {
			return $data;
		}
	}

	public static function getImage($url = '', $fileName = '')
	{
		$fileName = API_ROOT . '/Public/static/upload/wechat/' . $fileName . '.jpg';
		$ch = curl_init();
		$fp = fopen($fileName, 'wb');
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);

		curl_exec($ch);
		curl_close($ch);
		fclose($fp);
	}


}