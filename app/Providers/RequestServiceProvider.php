<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class RequestServiceProvider extends ServiceProvider
{
    /**
     * 引导服务。
     *
     * @return void
     */
    public function boot(): void
    {
        //region 强制定义为JSON请求
        /*Request::macro('expectsJson', function (): bool {
            return true;
        });

        Request::macro('wantsJson', function (): bool {
            return true;
        });*/
        //endregion

        /**
         * 判断是否移动设备
         */
        Request::macro('isMobile', function (): bool {
            $server = $this->server();
            // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
            if (isset ($server['HTTP_X_WAP_PROFILE'])) {
                return true;
            }
            // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
            if (isset ($server['HTTP_VIA'])) {
                return stristr($server['HTTP_VIA'], "wap") ? true : false;// 找不到为flase,否则为TRUE
            }
            // 判断手机发送的客户端标志,兼容性有待提高
            if (isset ($server['HTTP_USER_AGENT'])) {
                $clientkeywords = [
                    'mobile',
                    'nokia',
                    'sony',
                    'ericsson',
                    'mot',
                    'samsung',
                    'htc',
                    'sgh',
                    'lg',
                    'sharp',
                    'sie-',
                    'philips',
                    'panasonic',
                    'alcatel',
                    'lenovo',
                    'iphone',
                    'ipod',
                    'blackberry',
                    'meizu',
                    'android',
                    'netfront',
                    'symbian',
                    'ucweb',
                    'windowsce',
                    'palm',
                    'operamini',
                    'operamobi',
                    'openwave',
                    'nexusone',
                    'cldc',
                    'midp',
                    'wap',
                ];
                // 从HTTP_USER_AGENT中查找手机浏览器的关键字
                if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($server['HTTP_USER_AGENT']))) {
                    return true;
                }
            }
            if (isset ($server['HTTP_ACCEPT'])) { // 协议法，因为有可能不准确，放到最后判断
                // 如果只支持wml并且不支持html那一定是移动设备
                // 如果支持wml和html但是wml在html之前则是移动设备
                if ((strpos($server['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($server['HTTP_ACCEPT'], 'text/html') === false || (strpos($server['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($server['HTTP_ACCEPT'], 'text/html')))) {
                    return true;
                }
            }
            return false;
        });

        /**
         * 获取设备类型
         */
        Request::macro('getDeviceType', function (): string {
            // 全部变成小写字母
            $agent = strtolower($this->server('HTTP_USER_AGENT'));
            // 分别进行判断
            if (str_contains($agent, 'android')) {
                $type = 'android';
            } else if (str_contains($agent, 'iphone') || str_contains($agent, 'ipad')) {
                $type = 'ios';
            } else {
                $type = $this->isMobile() ? 'h5' : 'pc';
            }
            return $type;
            return $this->isMobile() ? 'h5' : 'pc';
        });

        /**
         * 获取当前请求的时间戳
         */
        Request::macro('time', function (bool $float = false): float|int {
            return $float ? request()->server('REQUEST_TIME_FLOAT') : request()->server('REQUEST_TIME');
        });

        /**
         * 获取客户端真实IP
         */
        Request::macro('getRealIpAddr', function (): string {
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { // to check ip is pass from proxy
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
            return $ip;
        });

        /*Request::macro('name', function () {
        });*/
    }

}
