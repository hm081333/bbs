<?php

namespace TieBa;

/**
 * 执行一个通配符表达式匹配
 * [可当preg_match()的简化版本去理解]
 * @param string $exp 匹配表达式
 * @param string $str 在这个字符串内运行匹配
 * @param int    $pat 规定匹配模式，0表示尽可能多匹配，1表示尽可能少匹配
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
 * @param string $text  完整的文本
 * @param string $left  左边文本
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
