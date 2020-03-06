<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2018-11-26
 * Time: 11:14:57
 */

namespace Common\Domain;


use Library\Traits\Domain;
use function Common\DI;

/**
 * 百度地图开放平台 领域层
 * Class BaiDuLBS
 * @package Common\Domain
 * @author  LYi-Ho 2018-11-26 11:14:57
 */
class BaiDuLBS
{
    use Domain;

    /**
     * @param $question string 问题
     * @return array
     */
    public static function get($question)
    {
        $tuling_url = 'http://www.tuling123.com/openapi/api';
        $setting = Setting::getSetting('tuling');
        $rs = DI()->curl->post($tuling_url, ['key' => $setting['api_key'], 'info' => $question]);
        $rs = json_decode($rs, true);
        return $rs;
    }

    /**
     * 地址换坐标
     * @param string $data
     * @return mixed|string
     */
    public static function address_to_location($data)
    {
        $baidu_map_config = Setting::getSetting('baidu_map');
        // API控制台申请得到的ak（此处ak值仅供验证参考使用）
        $ak = $baidu_map_config['ak'];
        // 应用类型为for server, 请求校验方式为sn校验方式时，系统会自动生成sk，可以在应用配置-设置中选择Security Key显示进行查看（此处sk值仅供验证参考使用）
        $sk = $baidu_map_config['sk'];
        $base_url = 'http://api.map.baidu.com';
        // get请求uri前缀
        $uri = '/geocoder/v2/';
        // 以Geocoding服务为例，地理编码的请求url，参数待填
        $url = "{$base_url}{$uri}?address=%s&output=%s&ak=%s&sn=%s";
        // 地理编码的请求output参数
        $output = 'json';
        // 构造请求串数组
        $param = [
            'address' => $data,
            'output' => $output,
            'ak' => $ak,
        ];
        // 调用sn计算函数，默认get请求
        $sn = self::caculateAKSN($ak, $sk, $uri, $param);
        // 请求参数中有中文、特殊字符等需要进行urlencode，确保请求串与sn对应
        $request = sprintf($url, urlencode($data), $output, $ak, $sn);
        // 执行请求的url
        $rs = DI()->curl->get($request);
        $rs = json_decode($rs, true);
        if ($rs['status'] !== 0) {
            DI()->logger->error('百毒地图API报错，错误码：' . $rs['status'] . '，错误信息：' . $rs['message']);
        }
        return $rs;
    }

    /**
     * 创建验证字符串
     * @param        $ak
     * @param        $sk
     * @param        $url
     * @param        $param
     * @param string $method
     * @return string
     */
    public static function caculateAKSN($ak, $sk, $url, $param, $method = 'GET')
    {
        if ($method === 'POST') {
            ksort($param);
        }
        $querystring = http_build_query($param);
        return md5(urlencode($url . '?' . $querystring . $sk));
    }

    /**
     * 坐标换地址
     * @param string $data
     * @return mixed|string
     */
    public static function location_to_address($data)
    {
        $baidu_map_config = Setting::getSetting('baidu_map');
        // API控制台申请得到的ak（此处ak值仅供验证参考使用）
        $ak = $baidu_map_config['ak'];
        // 应用类型为for server, 请求校验方式为sn校验方式时，系统会自动生成sk，可以在应用配置-设置中选择Security Key显示进行查看（此处sk值仅供验证参考使用）
        $sk = $baidu_map_config['sk'];
        $base_url = 'http://api.map.baidu.com';
        // get请求uri前缀
        $uri = '/geocoder/v2/';
        // 以Geocoding服务为例，地理编码的请求url，参数待填
        $url = "{$base_url}{$uri}?location=%s&output=%s&ak=%s&sn=%s";
        // 地理编码的请求output参数
        $output = 'json';
        // 构造请求串数组
        $param = [
            'location' => $data,
            'output' => $output,
            'ak' => $ak,
        ];
        // 调用sn计算函数，默认get请求
        $sn = self::caculateAKSN($ak, $sk, $uri, $param);
        // 请求参数中有中文、特殊字符等需要进行urlencode，确保请求串与sn对应
        $request = sprintf($url, urlencode($data), $output, $ak, $sn);
        // 执行请求的url
        $rs = DI()->curl->get($request);
        $rs = json_decode($rs, true);
        if ($rs['status'] !== 0) {
            DI()->logger->error('百毒地图API报错，错误码：' . $rs['status'] . '，错误信息：' . $rs['message']);
        }
        return $rs;
    }

}
