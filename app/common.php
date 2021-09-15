<?php
// 应用公共文件
use library\CUrl;
use think\Response;
use think\response\Json;

//region 目录，文件
/**
 * 扫描目录文件
 * @param string $dir 需要扫描的目录
 * @return array
 */
function scanFile(string $dir)
{
    $file_arr = scandir($dir);
    $new_arr = [];
    foreach ($file_arr as $item) {
        if ($item != ".." && $item != ".") {
            if (is_dir($dir . "/" . $item)) {
                $new_arr[$item] = scanFile($dir . "/" . $item);
            } else {
                $new_arr[] = $item;
            }
        }
    }
    return $new_arr;
}

/**
 * 根据路径创建目录或文件
 * @param string $path 需要创建目录路径
 */
function createDir($path)
{
    if (!is_dir($path) && !mkdir($path, 0777, true)) {
        \think\facade\Log::debug($path);
        createDir(dirname($path));
        mkdir($path, 0777, true);
    }
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

/**
 * 服务器本地路径
 * @param $path
 * @return string
 */
function server_path($path = '')
{
    return public_path() . $path;
}

/**
 * 资源路径
 * @param $path
 * @return string
 */
function res_path($path = '')
{
    if (\request()->server('HTTP_UPGRADE') == 'websocket') {
        //\Workerman\Worker::getStatus() == \Workerman\Worker::STATUS_RUNNING
        //10.0.0.20:8080/ws
        return \request()->server('HTTP_ORIGIN') . '/api/' . $path;
    } else {
        $url_root = (!empty(\request()->server('HTTPS')) && 'on' === \request()->server('HTTPS') ? 'https' : 'http') . '://' . \request()->server('HTTP_HOST') . (dirname(dirname(\request()->server('PHP_SELF'))) == '\\' ? '/' : dirname(\request()->server('PHP_SELF')));
        return config('app.cdn_root', $url_root) . $path;
    }
}

//endregion

//region 加密，密码
/**
 * openssl加密
 * @param string $data
 * @return string
 */
function opensslEncrypt(string $data): string
{
    $config = config('encrypt.stores.openssl');
    $encrypted = openssl_encrypt($data,
        $config['cipher_algo'],
        $config['passphrase'],
        $config['options'],
        $config['iv'],
        $config['tag'],
        $config['aad'],
        $config['tag_length']);
    $encode = base64_encode($encrypted);
    //openssl_encrypt 加密相当于将 mcrypt_encrypt 的加密结果执行一次 base64_encode
    return $encode;
}

/**
 * openssl解密
 * @param string $data
 * @return string
 */
function opensslDecrypt(string $data): string
{
    $config = config('encrypt.stores.openssl');
    //openssl_decode 解密相当于 先将加密结果执行一次base64_decode 然后再通过mcrypt_decrypt 解密
    $encryptedData = base64_decode($data);
    $decrypted = openssl_decrypt($encryptedData,
        $config['cipher_algo'],
        $config['passphrase'],
        $config['options'],
        $config['iv'],
        $config['tag'],
        $config['aad']);
    //$decrypted = rtrim($decrypted, "\0");//解密出来的数据后面会出现如图所示的六个红点；这句代码可以处理掉，从而不影响进一步的数据操作
    return $decrypted;
}

/**
 * 密码加密方法
 * @param string $password 需要加密的密码
 * @param string $algo 加密模式
 * @param array $options 加密选项
 * @return false|string|null
 */
function pwd_hash(string $password, $algo = PASSWORD_DEFAULT, array $options = [])
{
    return password_hash($password, $algo, $options);
}

/**
 * 密码验证方法
 * @param string $password 需要对比的密码
 * @param string $hash 加密后的密码
 * @return bool
 */
function pwd_verify(string $password, string $hash): bool
{
    return password_verify($password, $hash);
}

//endregion

//region 响应
/**
 * @param string $msg
 * @param array $data
 * @param int $code
 * @return Json
 */
function success($msg = '', $data = [], $code = 200): Json
{
    return \json([
        'ret' => $code,
        'data' => $data,
        'msg' => $msg
    ]);
}

//endregion

//region 快捷调用方法
function curl($retryTimes = 1, $timeoutMs = 3000)
{
    return new CUrl($retryTimes, $timeoutMs);
}

function T($str)
{
    return $str;
}

//endregion

//region 自定义方法
/**
 * 使用反斜线引用字符串或数组以便于SQL查询
 * 只引用'和\
 * @param string|array $s 需要转义的
 * @return string|array 转义结果
 */
function sqlAdds($s)
{
    if (is_array($s)) {
        $r = [];
        foreach ($s as $key => $value) {
            $k = str_replace('\'', '\\\'', str_replace('\\', '\\\\', $value));
            if (!is_array($value)) {
                $r[$k] = str_replace('\'', '\\\'', str_replace('\\', '\\\\', $value));
            } else {
                $r[$k] = sqlAdds($value);
            }
        }
        return $r;
    } else {
        return str_replace('\'', '\\\'', str_replace('\\', '\\\\', $s));
    }
}

/**
 * 获取两段文本之间的文本
 * @param string $text 完整的文本
 * @param string $left 左边文本
 * @param string $right 右边文本
 * @return string “左边文本”与“右边文本”之间的文本
 */
function textMiddle($text, $left, $right)
{
    $loc1 = stripos($text, $left);
    if (is_bool($loc1)) {
        return "";
    }
    $loc1 += strlen($left);
    $loc2 = stripos($text, $right, $loc1);
    if (is_bool($loc2)) {
        return "";
    }
    return substr($text, $loc1, $loc2 - $loc1);
}

/**
 * 执行一个通配符表达式匹配
 * [可当preg_match()的简化版本去理解]
 * @param string $exp 匹配表达式
 * @param string $str 在这个字符串内运行匹配
 * @param int $pat 规定匹配模式，0表示尽可能多匹配，1表示尽可能少匹配
 * @return array 匹配结果，$matches[0]将包含完整模式匹配到的文本， $matches[1] 将包含第一个捕获子组匹配到的文本，以此类推。
 */
function easy_match($exp, $str, $pat = 0)
{
    $exp = str_ireplace('\\', '\\\\', $exp);
    $exp = str_ireplace('/', '\/', $exp);
    $exp = str_ireplace('?', '\?', $exp);
    $exp = str_ireplace('<', '\<', $exp);
    $exp = str_ireplace('>', '\>', $exp);
    $exp = str_ireplace('^', '\^', $exp);
    $exp = str_ireplace('$', '\$', $exp);
    $exp = str_ireplace('+', '\+', $exp);
    $exp = str_ireplace('(', '\(', $exp);
    $exp = str_ireplace(')', '\)', $exp);
    $exp = str_ireplace('[', '\[', $exp);
    $exp = str_ireplace(']', '\]', $exp);
    $exp = str_ireplace('|', '\|', $exp);
    $exp = str_ireplace('}', '\}', $exp);
    $exp = str_ireplace('{', '\{', $exp);
    if ($pat == 0) {
        $z = '(.*)';
    } else {
        $z = '(.*?)';
    }
    $exp = str_ireplace('*', $z, $exp);
    $exp = '/' . $exp . '/';
    preg_match($exp, $str, $r);
    return $r;
}

/**
 * 根据时间获取打招呼方式
 * @param false $h 时间，小时
 * @return string
 */
function getGreeting($h = false)
{
    $h = $h ?: date('G', time());
    if ($h < 11) {
        $greeting = '早上好！';
    } else if ($h < 13) {
        $greeting = '中午好！';
    } else if ($h < 17) {
        $greeting = '下午好！';
    } else {
        $greeting = '晚上好！';
    }
    return $greeting;
}

//endregion

//region 枚举
/**
 * 性别
 * @param false $type
 * @return string|string[]
 */
function sexName($type = false)
{
    $sex = [
        1 => '男',
        2 => '女',
    ];
    if ($type !== false) {
        return $sex[$type] ?? '未知';
    }
    return $sex;
}
//endregion
