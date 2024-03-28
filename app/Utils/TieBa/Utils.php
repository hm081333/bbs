<?php

namespace App\Utils\TieBa;

class Utils
{
    /**
     * 执行一个通配符表达式匹配
     * [可当preg_match()的简化版本去理解]
     *
     * @param string $exp 匹配表达式
     * @param string $str 在这个字符串内运行匹配
     * @param int    $pat 规定匹配模式，0表示尽可能多匹配，1表示尽可能少匹配
     *
     * @return array 匹配结果，$matches[0]将包含完整模式匹配到的文本， $matches[1] 将包含第一个捕获子组匹配到的文本，以此类推。
     */
    public static function easyMatch($exp, $str, $pat = 0)
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
     * 使用反斜线引用字符串或数组
     *
     * @param string|array $s 需要转义的字符串或数组
     *
     * @return string|array 转义结果
     */
    public static function adds($s)
    {

        if (is_array($s)) {
            return array_map('addslashes', $s);
        } else {
            return addslashes($s);
        }
    }

    /**
     * 使用反斜线引用字符串或数组以便于SQL查询
     * 只引用'和\
     *
     * @param string|array $s 需要转义的
     *
     * @return string|array 转义结果
     */
    public static function sqlAdds($s)
    {
        if (is_array($s)) {
            $r = [];
            foreach ($s as $key => $value) {
                $k = str_replace('\'', '\\\'', str_replace('\\', '\\\\', $value));
                if (!is_array($value)) {
                    $r[$k] = str_replace('\'', '\\\'', str_replace('\\', '\\\\', $value));
                } else {
                    $r[$k] = static::sqlAdds($value);
                }
            }
            return $r;
        } else {
            return str_replace('\'', '\\\'', str_replace('\\', '\\\\', $s));
        }
    }

    /**
     * 获取两段文本之间的文本
     *
     * @param string $text  完整的文本
     * @param string $left  左边文本
     * @param string $right 右边文本
     *
     * @return string “左边文本”与“右边文本”之间的文本
     */
    public static function textMiddle($text, $left, $right)
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
     * [已搬走]MySQL 随机取记录
     * 请查看S::rand()
     *
     * @param string $t 表
     * @param string $c ID列，默认为id
     * @param int    $n 取多少个
     * @param string $w 条件语句
     * @param bool   $f 是否强制以多维数组形式返回，默认false
     * @param string $p 随机数据前缀，如果产生冲突，请修改本项
     *
     * @return array 取1个直接返回结果数组(除非$f为true)，取>1个返回多维数组，用foreach取出
     */
    public static function randRow($t, $c = 'id', $n = 1, $w = '', $f = false, $p = 'tempval_')
    {

        global $m;
        return $m->rand($t, $c, $n, $w, $f, $p);
    }

    /**
     * 格式化输入bduss字符串
     *
     * @param string $bduss
     *
     * @return string
     */
    public static function parse_bduss(string $bduss): string
    {
        // 去除双引号和bduss=
        $bduss = str_replace('"', '', $bduss);
        $bduss = str_ireplace('BDUSS=', '', $bduss);
        $bduss = str_replace(' ', '', $bduss);
        return static::sqlAdds($bduss);
    }

    /**
     * 格式化输入stoken字符串
     *
     * @param string $stoken
     *
     * @return string
     */
    public static function parse_stoken(string $stoken): string
    {
        // 去除双引号和stoken=
        $stoken = str_replace('"', '', $stoken);
        $stoken = str_ireplace('STOKEN=', '', $stoken);
        $stoken = str_replace(' ', '', $stoken);
        return static::sqlAdds($stoken);
    }

    /**
     * 获取一个bduss对应的百度用户名
     *
     * @param string $bduss BDUSS
     *
     * @return string 百度用户名，失败返回""
     */
    public static function getBaiduId($bduss)
    {
        //$c = new wcurl('http://top.baidu.com/user/pass');
        //$c->addCookie(array('BDUSS' => $bduss));
        //$data = $c->get();
        //$c->close();
        $userData = static::getBaiduUserInfo($bduss);
        return isset($userData["name"]) ? $userData["name"] : "";
    }

    /**
     * 获取一个bduss对应的百度用户信息
     *
     * @param string $bduss BDUSS
     *
     * @return array|bool 百度用户信息，失败返回FALSE
     */
    public static function getBaiduUserInfo($bduss)
    {
        $c = new Curl('http://c.tieba.baidu.com/c/s/login');
        $c->addCookie(['BDUSS' => $bduss]);
        $temp = ['bdusstoken' => $bduss];
        Misc::addTiebaSign($temp);
        $data = $c->post($temp);
        $c->close();
        $data = json_decode($data, true);
        $data = isset($data["user"]) ? $data["user"] : false;
        return $data;
    }

    /**
     * 获取指定 portrait/baiduid 的用户信息
     *
     * @param string $id        portrait/baiduid
     * @param bool   $isBaiduId $id是否为百度id
     *
     * @return array 用户信息
     */
    public static function getUserInfo($id, $isBaiduId = true)
    {
        $user = new Curl("https://tieba.baidu.com/home/get/panel?ie=utf-8&" . ($isBaiduId ? "un={$id}" : "id={$id}"));
        $re = $user->get();
        return json_decode($re, true);
    }
}
