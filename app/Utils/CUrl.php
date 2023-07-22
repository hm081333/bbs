<?php

namespace App\Utils;


use Exception;

/**
 * Class CUrl
 * CUrl CURL请求类
 * 通过curl实现的快捷方便的接口请求类
 * @package library
 */
class CUrl
{

    /**
     * 最大重试次数
     */
    const MAX_RETRY_TIMES = 10;

    /**
     * @var int $retryTimes 超时重试次数；注意，此为失败重试的次数，即：总次数 = 1 + 重试次数
     */
    protected $retryTimes;
    protected $noRetry = false;
    /**
     * @var int $timeoutMs 超时时间，单位：毫秒
     */
    protected $timeoutMs;
    protected $timeout = 0;

    protected $header = [];

    protected $option = [];

    protected $hascookie = false;

    protected $cookie = [];

    /**
     * CUrl constructor.
     * @param int $retryTimes 超时重试次数，默认为1
     * @param int $timeoutMs  超时时间，单位：毫秒，默认为3000
     * @throws Exception
     */
    public function __construct($retryTimes = 1, $timeoutMs = 5000)
    {
        if (!function_exists('curl_exec')) {
            throw new Exception('服务器不支持cURL');
        }
        $this->retryTimes = $retryTimes < static::MAX_RETRY_TIMES ? $retryTimes : static::MAX_RETRY_TIMES;
        $this->timeoutMs = $timeoutMs;
    }

    /** ------------------ 核心使用方法 ------------------ **/

    /**
     * GET方式的请求
     * @param string $url       请求的链接
     * @param int    $timeoutMs 超时设置，单位：毫秒
     * @return string 接口返回的内容，超时返回false
     * @throws Exception
     */
    public function get($url, $timeoutMs = 5000)
    {
        return $this->request($url, [], $timeoutMs);
    }

    /**
     * 统一接口请求
     * @param string $url       请求的链接
     * @param array  $data      POST的数据
     * @param int    $timeoutMs 超时设置，单位：毫秒
     * @return string 接口返回的内容，超时返回false
     * @throws Exception
     */
    protected function request($url, $data = [], $timeoutMs = 5000)
    {
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => 0,
            CURLOPT_CONNECTTIMEOUT_MS => $timeoutMs,
            CURLOPT_TIMEOUT_MS => $this->timeout,
            CURLOPT_HTTPHEADER => $this->getHeaders(),

            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            CURLOPT_COOKIE => $this->getCookies(),

            // CURLOPT_PROXY => '127.0.0.1',
            // CURLOPT_PROXYPORT => '1081',
            // CURLOPT_PROXYUSERPWD => '',
        ];

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
        curl_setopt_array($ch, $options);
        $curRetryTimes = $this->retryTimes;
        do {
            $rs = curl_exec($ch);
            $curRetryTimes--;
        } while ($rs === false && $curRetryTimes >= 0 && !$this->noRetry);
        $errno = curl_errno($ch);
        if ($errno) {
            throw new Exception(sprintf("%s::%s(%d)\n", $url, curl_error($ch), $errno));
        }

        //update cookie
        if ($this->hascookie) {
            $cookie = $this->getRetCookie(curl_getinfo($ch, CURLINFO_COOKIELIST));
            !empty($cookie) && $this->cookie = $cookie + $this->cookie;
            $this->hascookie = false;
            unset($this->header['Cookie']);
            unset($this->option[CURLOPT_COOKIEFILE]);
        }
        if ($this->header) {
            $this->header = [];
        }
        if ($this->option) {
            $this->option = [];
        }
        // 重置不重试选项
        $this->noRetry = false;
        // 重置为不超时
        $this->timeout = 0;
        curl_close($ch);

        return $rs;
    }

    /**
     *
     * @return array
     */
    protected function getHeaders()
    {
        $arrHeaders = [];
        foreach ($this->header as $key => $val) {
            $arrHeaders[] = $key . ': ' . $val;
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

    protected function getRetCookie(array $cookies)
    {
        $ret = [];
        foreach ($cookies as $cookie) {
            $arr = explode("\t", $cookie);
            if (!isset($arr[6])) {
                continue;
            }
            $ret[$arr[5]] = $arr[6];
        }
        return $ret;
    }

    /** ------------------ 前置方法 ------------------ **/

    public function json_get($url, $timeoutMs = 5000)
    {
        return Tools::json_decode($this->request($url, [], $timeoutMs));
    }

    /**
     * POST方式的请求
     * @param string $url       请求的链接
     * @param array  $data      POST的数据
     * @param int    $timeoutMs 超时设置，单位：毫秒
     * @return string 接口返回的内容，超时返回false
     * @throws Exception
     */
    public function post($url, $data, $timeoutMs = 5000)
    {
        return $this->request($url, $data, $timeoutMs);
    }

    public function json_post($url, $data, $timeoutMs = 5000)
    {
        return Tools::json_decode($this->request($url, $data, $timeoutMs));
    }

    /**
     * 获取文件
     * @param string $url
     * @param string $path
     * @param string $file_name
     * @param int    $timeoutMs
     * @return string
     * @throws Exception
     */
    public function getFile(string $url, string $path, string $file_name, int $timeoutMs = 30000)
    {
        if (empty($path) || empty($file_name)) {
            throw new Exception('路径或文件名为空');
        }
        if (substr($path, -1, 1) != '/' || substr($path, -1, 1) != '\\') $path .= '/';
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $fp = fopen($path . $file_name, 'wb');
        if ($fp === false) {
            throw new Exception('保存文件初始化失败');
        }
        $this->setOption([CURLOPT_URL => $url, CURLOPT_FILE => $fp, CURLOPT_HEADER => false, CURLOPT_FOLLOWLOCATION => true, CURLOPT_CONNECTTIMEOUT_MS => $timeoutMs]);
        $this->request($url);
        fclose($fp);
        return $path . $file_name;
    }

    /**
     * 设置curl配置项
     *
     * - 1、后设置的会覆盖之前的设置
     * - 2、开发者设置的会覆盖框架的设置
     *
     * @param array $option 格式同上
     *
     * @return $this
     */
    public function setOption($option)
    {
        $this->option = $option + $this->option;
        return $this;
    }

    public function unsetHeader($key)
    {
        unset($this->header[$key]);
        return $this;
    }

    /**
     * 设置不重试
     * @return CUrl
     */
    public function setNoRetry()
    {
        $this->noRetry = true;
        return $this;
    }

    /**
     * 设置程序超时时间
     * @param int $timeoutMs
     * @return CUrl
     */
    public function setTimeout($timeoutMs = 5000)
    {
        $this->timeout = $timeoutMs;
        return $this;
    }

    /** ------------------ 辅助方法 ------------------ **/

    public function withCookies()
    {
        $this->hascookie = true;

        if (!empty($this->cookie)) {
            $this->setHeader(['Cookie' => $this->getCookieString()]);
        }
        $this->setOption([CURLOPT_COOKIEFILE => '']);

        return $this;
    }

    /**
     * 设置请求头，后设置的会覆盖之前的设置
     *
     * @param array $header 传入键值对如：
     *                      ```
     *                      array(
     *                      'Accept' => 'text/html',
     *                      'Connection' => 'keep-alive',
     *                      )
     *                      ```
     *
     * @return $this
     */
    public function setHeader($header)
    {
        $this->header = array_merge($this->header, $header);
        return $this;
    }

    protected function getCookieString()
    {
        $ret = '';
        foreach ($this->getCookie() as $key => $val) {
            $ret .= $key . '=' . $val . ';';
        }
        return trim($ret, ';');
    }

    /**
     * @return array
     */
    public function getCookie()
    {
        return $this->cookie;
    }

    /**
     * @param array $cookie
     */
    public function setCookie($cookie)
    {
        $this->cookie = array_merge($this->cookie, $cookie);
        return $this;
    }

    /**
     * 设置引用地址
     * @param string $referer
     * @return $this
     */
    public function setReferer($referer)
    {
        $this->header['Referer'] = $referer;
        return $this;
    }
}
