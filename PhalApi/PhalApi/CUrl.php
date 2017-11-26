<?php
/**
 * PhalApi_CUrl CURL请求类
 *
 * 通过curl实现的快捷方便的接口请求类
 *
 * <br>示例：<br>
 *
 * ```
 *  // 失败时再重试2次
 *  $curl = new PhalApi_CUrl(2);
 *
 *  // GET
 *  $rs = $curl->get('http://phalapi.oschina.mopaas.com/Public/demo/?service=Default.Index');
 *
 *  // POST
 *  $data = array('user_name' => 'dogstar');
 *  $rs = $curl->post('http://phalapi.oschina.mopaas.com/Public/demo/?service=Default.Index', $data);
 * ```
 *
 * @package     PhalApi\CUrl
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-01-02
 */

class PhalApi_CUrl
{

	/**
	 * 最大重试次数
	 */
	const MAX_RETRY_TIMES = 10;

	/**
	 * @var int $retryTimes 超时重试次数；注意，此为失败重试的次数，即：总次数 = 1 + 重试次数
	 */
	protected $retryTimes;

	protected $header = array();
	protected $cookie = array();
	protected $option = array();

	/**
	 * 设置请求头，后设置的会覆盖之前的设置
	 *
	 * @param array $header 传入键值对如：
	 * ```
	 * array(
	 *     ['Accept'=>'text/html'],
	 *     ['Connection'=>'keep-alive'],
	 * )
	 * ```
	 *
	 * @return $this
	 */
	public function setHeader($header)
	{
		$this->header = array_merge($this->header, $header);
		return $this;
	}

	public function unsetHeader($key)
	{
		unset($this->header[$key]);
		return $this;
	}

	/**
	 * 设置curl配置项
	 * 1、后设置的会覆盖之前的设置
	 * 2、开发者设置的会覆盖框架的设置
	 * @param array $option 格式同上
	 *
	 * @return $this
	 */
	public function setOption($option)
	{
		//$this->option = array_merge($this->option, $option); // array_merge会改变key值，故弃用
		$this->option = $option + $this->option; // 后传入的优先
		return $this;
	}

	/**
	 * @param array $cookie
	 * @return $this
	 */
	public function setCookie($cookie)
	{
		$this->cookie = array_merge($this->cookie, $cookie);
		return $this;
	}

	/**
	 * @param int $retryTimes 超时重试次数，默认为5
	 */
	public function __construct($retryTimes = 5)
	{
		if (!function_exists('curl_exec')) {
			throw new PhalApi_Exception_InternalServerError('服务器不支持cURL');
		}
		$this->retryTimes = $retryTimes < static::MAX_RETRY_TIMES
			? $retryTimes : static::MAX_RETRY_TIMES;
	}

	/**
	 * GET方式的请求
	 * @param string $url 请求的链接
	 * @param int $timeoutMs 超时设置，单位：毫秒
	 * @return string 接口返回的内容，超时返回false
	 */
	public function get($url, $timeoutMs = 3000)
	{
		return $this->request($url, array(), $timeoutMs);
	}

	public function json_get($url, $timeoutMs = 3000)
	{
		return json_decode($this->request($url, array(), $timeoutMs), true);
	}

	/**
	 * POST方式的请求
	 * @param string $url 请求的链接
	 * @param array $data POST的数据
	 * @param int $timeoutMs 超时设置，单位：毫秒
	 * @return string 接口返回的内容，超时返回false
	 */
	public function post($url, $data, $timeoutMs = 3000)
	{
		return $this->request($url, $data, $timeoutMs);
	}

	public function json_post($url, $data, $timeoutMs = 3000)
	{
		return json_decode($this->request($url, $data, $timeoutMs), true);
	}

	/**
	 * 获取文件
	 * @param $url
	 * @param int $timeoutMs
	 * @return string
	 */
	public function getFile($url, $path, $file_name, $timeoutMs = 3000)
	{
		if (empty($path) || empty($file_name)) {
			throw new PhalApi_Exception_InternalServerError('路径或文件名为空');
		}
		if (!is_dir($path)) {
			mkdir($path, 0777, TRUE);
		}
		$fp = fopen($path . $file_name, 'wb');
		if ($fp === FALSE) {
			throw new PhalApi_Exception_InternalServerError('保存文件初始化失败');
		}
		$this->setOption(array(CURLOPT_URL => $url, CURLOPT_FILE => $fp, CURLOPT_HEADER => FALSE, CURLOPT_FOLLOWLOCATION => TRUE, CURLOPT_CONNECTTIMEOUT_MS => $timeoutMs));
		$this->request($url);
		fclose($fp);
		return true;
	}

	/**
	 *
	 * @return array
	 */
	protected function getHeaders()
	{
		$arrHeaders = array();
		foreach ($this->header as $key => $val) {
			$arrHeaders[] = $key . ':' . $val;
		}
		return $arrHeaders;
	}

	protected function getCookies()
	{
		$cookies = '';
		if (is_array($this->cookie)) {
			foreach ($this->cookie as $key => $val) {
				$cookies .= "{$key}={$val}; ";
			}
		} else {
			$cookies = $this->cookie;
		}
		return $cookies;
	}

	/**
	 * 统一接口请求
	 * @param string $url 请求的链接
	 * @param array $data POST的数据
	 * @param int $timeoutMs 超时设置，单位：毫秒
	 * @return string 接口返回的内容，超时返回false
	 */
	protected function request($url, $data = array(), $timeoutMs = 3000)
	{
		$options = array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => TRUE, //将curl获取的信息以文件流的形式返回，而不是直接输出
			CURLOPT_SSL_VERIFYPEER => FALSE,
			CURLOPT_HEADER => FALSE,
			CURLOPT_CONNECTTIMEOUT_MS => $timeoutMs,
			CURLOPT_HTTPHEADER => $this->getHeaders(), //字符串
			//CURLOPT_HTTPHEADER => $this->header, //数组
			CURLOPT_COOKIE => $this->getCookies(),
		);

		if (!empty($data)) {
			$options[CURLOPT_POST] = 1;
			if (is_array($data)) {
				$options[CURLOPT_POSTFIELDS] = http_build_query($data);//使用给出的关联（或下标）数组生成一个经过 URL-encode 的请求字符串
			} else {
				$options[CURLOPT_POSTFIELDS] = $data; // 字符串
			}
		}

		if (isset($this->option[CURLOPT_FILE])) {
			$options = $this->option;
		} else {
			$options = $this->option + $options;//$this->>option优先
		}

		$ch = curl_init();
		if ($ch === FALSE) {
			throw new PhalApi_Exception_InternalServerError('初始化cURL失败');
		}
		curl_setopt_array($ch, $options);
		$curRetryTimes = $this->retryTimes;
		do {
			$rs = curl_exec($ch);
			$curRetryTimes--;
		} while ($rs === FALSE && $curRetryTimes >= 0);

		curl_close($ch);
		return $rs;
	}
}
