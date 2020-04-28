<?php

namespace Common;

use Library\Crypt\RSA\KeyGenerator;
use Library\DependenceInjection;
use Library\Tool\HtmlCompress;

/**
 * 获取DI
 * 相当于DependenceInjection::one()
 * @return DependenceInjection
 */
function DI()
{
    return DependenceInjection::one();
}

/**
 * 解密gzip二进制字符串
 * @param $str
 * @return string
 */
function gzip_binary_string_decode($str)
{
    return zlib_decode(mb_convert_encoding($str, 'ISO-8859-1', 'utf-8'));
}

/**
 * 生成gzip二进制字符串
 * @param     $str
 * @param int $encoding
 * @return false|string|string[]|null
 */
function gzip_binary_string_encode($str, $encoding = ZLIB_ENCODING_DEFLATE)
{
    return mb_convert_encoding(zlib_encode($str, $encoding), 'utf-8', 'ISO-8859-1');
}

/**
 * 多维数组合并
 * @param array $array1
 * @param array $array2
 * @return array
 */
function multi_array_merge($array1, $array2)
{
    foreach ($array2 as $key => $value) {
        if (is_array($value) && (isset($array1[$key]) && is_array($array1[$key]))) {
            $array1[$key] = multi_array_merge($array1[$key], $value);
        } else {
            $array1[$key] = $value;
        }
    }
    return $array1;
}

function getComposerRequire()
{
    $composer = file_get_contents(API_ROOT . '/composer.json');
    // 解码JSON字符串
    $composer = json_decode($composer, true);
    // composer引入类库
    // $require = $composer['require'];
    // 类库名称
    // $require_name = array_keys($require);
    $require = [
        'ext' => [],
        'packages' => [],
    ];
    foreach ($composer['require'] as $name => $item) {
        if (strpos($name, 'ext-') !== false) {
            $require['ext'][] = str_replace('ext-', '', $name);
        } else if ($name != 'php') {
            $require['packages'][] = $name;
        }
    }
    return $require;
}

function arr_unix_formatter(array $arr)
{
    $format_fields = DI()->config->get('app.unix_time_format_field');
    foreach (array_keys($arr) as $key_name) {
        if (in_array($key_name, $format_fields)) {
            $arr["{$key_name}_unix"] = $arr[$key_name];// 时间戳
            $arr["{$key_name}_date"] = unix_formatter($arr[$key_name]);
            $arr[$key_name] = unix_formatter($arr[$key_name], true);
        }
    }
    return $arr;
}

function unix_formatter($time = false, $full = false)
{
    if ($time <= 0) {
        // return '-';
        return '';
    }
    if ($full) {
        return date('Y-m-d H:i:s', $time);
    } else {
        return date('Y-m-d', $time);
    }
}

/**
 * 服务器本地路径
 * @param $path
 * @return string
 */
function server_path($path = '')
{
    return API_ROOT . '/public/' . $path;
}

/**
 * 资源路径
 * @param $path
 * @return string
 */
function res_path($path = '')
{
    //\Workerman\Worker::getStatus() == \Workerman\Worker::STATUS_RUNNING
    if ($_SERVER['HTTP_UPGRADE'] == 'websocket') {
        //10.0.0.20:8080/ws
        return $_SERVER['HTTP_ORIGIN'] . '/api/' . $path;
    }
    return URL_ROOT . '/' . $path;
}

/**
 * 根据路径创建目录或文件
 * @param string $path 需要创建目录路径
 */
function createDir($path)
{
    if (!is_dir($path) && !mkdir($path, 0777, true)) {
        DI()->logger->debug($path);
        createDir(dirname($path));
        mkdir($path, 0777, true);
    }
}

/**
 * 生成openssl证书
 */
function create_openssl_pkey()
{
    $config = DI()->config->get('sys.openssl');// 配置信息
    $config_args = $config['config'];// 用来调整导出流程，通过指定或者覆盖openssl配置文件选项
    $pkey = new KeyGenerator($config_args);
    file_put_contents($config['privateKey'], $pkey->getPriKey());// 把私钥写入指定路径文件
    file_put_contents($config['publicKey'], $pkey->getPubKey());// 把公钥写入指定路径文件
}

/**
 * openssl加密
 * @param string $data
 * @return string
 */
function encrypt(string $data, string $method = 'AES-256-CFB')
{
    $encrypted = openssl_encrypt($data, $method, SECURITY_KEY, OPENSSL_ZERO_PADDING, SECURITY_IV);
    $encode = base64_encode($encrypted);
    //openssl_encrypt 加密相当于将 mcrypt_encrypt 的加密结果执行一次 base64_encode
    return $encode;
}

/**
 * openssl解密
 * @param string $data
 * @return string
 */
function decrypt(string $data, string $method = 'AES-256-CFB')
{
    //openssl_decode 解密相当于 先将加密结果执行一次base64_decode 然后再通过mcrypt_decrypt 解密
    $encryptedData = base64_decode($data);
    $decrypted = openssl_decrypt($encryptedData, $method, SECURITY_KEY, OPENSSL_ZERO_PADDING, SECURITY_IV);
    //$decrypted = rtrim($decrypted, "\0");//解密出来的数据后面会出现如图所示的六个红点；这句代码可以处理掉，从而不影响进一步的数据操作
    return $decrypted;
}

/**
 * 密码加密方法
 * @param string     $password 需要加密的密码
 * @param string|int $algo     加密模式
 * @param array|NULL $options  加密选项
 * @return bool|string
 */
function pwd_hash(string $password, $algo = PASSWORD_DEFAULT, array $options = null)
{
    if (empty($options)) {
        return password_hash($password, $algo);
    } else {
        return password_hash($password, $algo, $options);
    }
}

/**
 * 密码验证方法
 * @param string $password 需要对比的密码
 * @param string $hash     加密后的密码
 * @return bool
 */
function pwd_verify(string $password, string $hash)
{
    return password_verify($password, $hash);
}

/**
 * 返回404
 */
function fourZeroFour()
{
    // header('HTTP/1.1 404 Not Found');
    DI()->response->setRet(404);
    DI()->response->adjustHttpStatus();
    if (!IS_AJAX) {
        showFourZeroFourPage();
    }
    exit();
}

function showFourZeroFourPage()
{
    ob_start();
    ob_implicit_flush(false);

    $view = server_path('404.html');
    //检查文件是否存在
    file_exists($view) ? require $view : exit('404模板文件不存在');

    //获取当前缓冲区内容
    //$content = ob_get_contents(); // 仅输出
    $content = ob_get_clean(); // 输出并清空关闭
    $content = HtmlCompress::higrid_compress_html($content); // 正则删除无关代码
    $content = HtmlCompress::compress_html($content); // 正则删除无关代码
    exit($content);
}

/**
 * 是否处于微信环境
 * @return bool
 */
function isWeChat()
{
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
        return true;
    }
    return false;
}

/**
 * PHP-HTTP 断点续传 & 多线程下载 实现
 * @param string $path : 文件所在路径
 * @param string $file : 文件名
 * @return bool
 */
function download($path, $file)
{
    $real = $path . $file;
    if (!file_exists($real)) {
        fourZeroFour();
    }
    //注意文件不能大于2G
    $len = filesize($real);
    // $filename = basename($real);
    //类型设定为强制下载
    $ctype = "application/force-download";
    header("Cache-Control: public");
    header("Pragma: public");
    header("Content-Type: $ctype");
    //设定文件名
    //解决在IE中下载时中文乱码问题
    $ua = $_SERVER['HTTP_USER_AGENT'];
    if (preg_match('/MSIE/', $ua)) {// 表示正在使用 Internet Explorer。
        $ie_filename = str_replace('+', '%20', urlencode(basename($real)));
        header('Content-Disposition: attachment; filename=' . $ie_filename);
    } else {
        header("Content-Disposition: attachment; filename=" . basename($real));
    }
    // header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Accept-Ranges: bytes");
    header("Accept-Length: " . $len);

    //范围默认从0开始
    $range_start = 0;
    //范围默认包括结尾
    $range_end = $len - 1;
    $content_length = $len;
    //检查请求是否包含Range段，
    //如果包括，取出并修改$range_start和$range_end
    if (isset($_SERVER['HTTP_RANGE'])) { //http_range表示请求一个实体/文件的一个部分,用这个实现多线程下载和断点续传！
        [$a, $range_info] = explode("=", $_SERVER['HTTP_RANGE']);
        preg_match("/(\d*)-(\d*)/", $range_info, $matches);
        if ($matches[1]) {
            $range_start = (int)$matches[1];
        } else {
            if ($matches[2]) {
                $range_start = $len - (int)$matches[2];
            }
        }
        $content_length = $range_end - $range_start + 1;
        header("HTTP/1.1 206 Partial Content");
    } else {
        header("HTTP/1.1 200 OK");
    }
    header("Content-Length: $content_length");
    header("Content-Range: bytes $range_start-$range_end/$len");

    $fp = fopen("$real", "rb+");// 打开文件
    fseek($fp, $range_start); //fseek:在打开的文件中定位,该函数把文件指针从当前位置向前或向后移动到新的位置，新位置从文件头开始以字节数度量。成功则返回 0；否则返回 -1。注意，移动到 EOF 之后的位置不会产生错误。
    while (!feof($fp)) { //feof:检测是否已到达文件末尾 (eof)
        set_time_limit(0);
        print(fread($fp, 1024 * 8)); //读取文件（可安全用于二进制文件,第二个参数:规定要读取的最大字节数）
        flush(); //刷新缓冲区的内容(严格来讲, 这个只有在PHP做为apache的Module(handler或者filter)安装的时候, 才有实际作用. 它是刷新WebServer(可以认为特指apache)的缓冲区.)
        ob_flush(); //刷新PHP自身的缓冲区
    }
    fclose($fp);
    exit;
}

/**
 * 打印样式格式化的信息
 * @param $data
 */
function dump(...$args)
{
    echo '<pre/>';
    foreach ($args as $arg) {
        var_dump($arg);
    }
    exit();
}

/**
 * 根据URI获得模块名和命名空间
 * @param $uri
 * @return mixed
 */
function getModuleNameSpaceByURI($uri)
{
    // 模块过滤规则
    $moduleRule = DI()->config->get('sys.moduleRule');
    // 正则表达式
    $pattern = '/([\w]+)*([\?\=][\s\S]*)?$/';
    // 正则匹配
    preg_match($pattern, $uri, $matches);
    // 重定向的模块名称
    $module = $matches[1] ?? '';
    // 当前访问模块
    $return['module'] = strtolower(empty($module) ? $moduleRule['default'] : $module);
    // 当前访问模块 命名空间名称
    $return['name_space'] = $moduleRule['prefix'][$return['module']] ?? '';
    return $return;
}

/**
 * 清空目录以及子目录等所有文件--不删除目录
 * @param $path
 * @return bool
 */
function emptyDir($path)
{
    if (!is_dir($path)) {
        return false;
    }
    $dir = opendir($path);
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            $full = $path . '/' . $file;
            if (is_dir($full)) {
                deleteDir($full);
            } else {
                unlink($full);
            }
        }
    }
    closedir($dir);
    return true;
}

/**
 * 删除目录以及子目录等所有文件
 * - 请注意不要删除重要目录！
 * @param string $path 需要删除目录路径
 * @return bool
 */
function deleteDir($path)
{
    emptyDir($path);
    rmdir($path);
    return true;
}
