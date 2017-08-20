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
	public static function compress_html($string)
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
		//$rs = self::curl_request($tuling_config['URI'], array('key' => $tuling_config['APIkey'], 'info' => $question));
		$rs = DI()->curl->json_post($tuling_config['URI'], array('key' => $tuling_config['APIkey'], 'info' => $question));
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
		$rs = DI()->curl->json_get($request);
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

	/**
	 * @param $code string 快递公司代码
	 * @param $sn string 快递单号
	 */
	public static function getLogistics($code, $sn)
	{
		return DI()->curl->json_get('http://www.kuaidi100.com/query?type=' . $code . '&postid=' . $sn);
	}


}