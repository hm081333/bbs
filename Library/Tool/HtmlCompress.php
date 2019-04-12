<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2019-04-11
 * Time: 08:46:04
 */

namespace Tool;


/**
 *
 * Class HtmlCompress
 * @package Tool
 * @author  LYi-Ho 2019-04-11 08:46:04
 */
class HtmlCompress
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
     * @param $string string HTML内容
     * @return $string 压缩后HTML内容
     */
    public static function compress_html($string)
    {
        $string = str_replace("\r\n", '', $string); //清除换行符
        $string = str_replace("\n", '', $string); //清除换行符
        $string = str_replace("\t", '', $string); //清除制表符
        $pattern = [//去掉注释标记
            "/> *([^ ]*) *</",
            "/[\s]+/",
            "/<!--[\\w\\W\r\\n]*?-->/",
            "/\" /",
            "/ \"/",
            "'/\*[^*]*\*/'",
        ];
        $replace = [
            ">\\1<",
            " ",
            "",
            "\"",
            "\"",
            "",
        ];
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
}
