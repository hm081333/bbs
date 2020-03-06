<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2018-12-27
 * Time: 14:33:39
 */

namespace Sign\Domain;

use Common\Domain\BaiDuId;
use Library\Exception\BadRequestException;
use Library\Exception\Exception;
use Library\Exception\InternalServerErrorException;
use Library\Traits\Domain;
use function Common\DI;
use function PhalApi\T;

/**
 * Class QQLogin
 * @package Common\Domain
 * @author  LYi-Ho 2018-12-27 14:33:39
 */
class QQLogin
{
    use Domain;

    public static function getWxQrCode()
    {
        $url = 'https://open.weixin.qq.com/connect/qrconnect?appid=wx85f17c29f3e648bf&response_type=code&scope=snsapi_login&redirect_uri=https%3A%2F%2Fpassport.baidu.com%2Fphoenix%2Faccount%2Fafterauth&state=' . time() . '&display=page&traceid=';
        $ret = self::get_curl($url);
        preg_match('!connect/qrcode/(.*?)\"!', $ret, $match);
        if ($uuid = $match[1])
            return ['code' => 0, 'uuid' => $uuid, 'imgurl' => 'https://open.weixin.qq.com/connect/qrcode/' . $uuid];
        else
            return ['code' => 1, 'msg' => '获取二维码失败'];
    }

    private static function get_curl($url, $post = false, $referer = false, $cookie = false, $header = false, $ua = false, $nobaody = false, $split = false)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $httpheader[] = "Accept: application/json";
        $httpheader[] = "Accept-Encoding: gzip,deflate,sdch";
        $httpheader[] = "Accept-Language: zh-CN,zh;q=0.8";
        $httpheader[] = "Connection: close";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
        if ($post) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        if ($header) {
            curl_setopt($ch, CURLOPT_HEADER, true);
        }
        if ($cookie) {
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        }
        if ($referer) {
            curl_setopt($ch, CURLOPT_REFERER, $referer);
        }
        if ($ua) {
            curl_setopt($ch, CURLOPT_USERAGENT, $ua);
        } else {
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.152 Safari/537.36');
        }
        if ($nobaody) {
            curl_setopt($ch, CURLOPT_NOBODY, 1);

        }
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $ret = curl_exec($ch);
        if ($split) {
            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header = substr($ret, 0, $headerSize);
            $body = substr($ret, $headerSize);
            $ret = [];
            $ret['header'] = $header;
            $ret['body'] = $body;
        }
        curl_close($ch);
        return $ret;
    }

    /**
     * @param string $uuid
     * @param string $last
     * @return array
     * @throws BadRequestException
     */
    public static function wxLogin(string $uuid = '', string $last = '')
    {
        if (empty($uuid)) {
            throw new BadRequestException(T('uuid不能为空'));
        }
        $param = ['uuid' => $uuid];
        if (!empty($last)) {
            $param['last'] = $last;
        }
        $url = 'https://long.open.weixin.qq.com/connect/l/qrconnect?' . http_build_query($param) . '&_=' . time() . '000';
        $ret = self::get_curl($url, 0, 'https://open.weixin.qq.com/connect/qrconnect');
        if (preg_match("/wx_errcode=(\d+);window.wx_code=\'(.*?)\'/", $ret, $match)) {
            $errcode = $match[1];
            $code = $match[2];
            if ($errcode == 408) {
                return ['code' => '1', 'msg' => '二维码未失效'];
            } else if ($errcode == 404) {
                return ['code' => '2', 'msg' => '请在微信中点击确认即可登录'];
            } else if ($errcode == 402) {
                return ['code' => '3', 'msg' => '二维码已失效'];
            } else if ($errcode == 405) {
                $data = self::get_curl('https://passport.baidu.com/phoenix/account/startlogin?type=42&tpl=pp&u=https%3A%2F%2Fpassport.baidu.com%2F&display=popup&act=optional', 0, 0, 0, 1);
                preg_match('/mkey=(.*?);/', $data, $mkey);
                if ($mkey = $mkey[1]) {
                    $url = 'https://passport.baidu.com/phoenix/account/afterauth?mkey=' . $mkey . '&appid=wx85f17c29f3e648bf&traceid=&code=' . $code . '&state=' . time();
                    $data = self::get_curl($url, 0, 0, 'mkey=' . $mkey . ';', 1);
                    preg_match('/BDUSS=(.*?);/', $data, $BDUSS);
                    preg_match('/STOKEN=(.*?);/', $data, $STOKEN);
                    preg_match('/PTOKEN=(.*?);/', $data, $PTOKEN);
                    preg_match('/passport_uname: \'(.*?)\'/', $data, $uname);
                    preg_match('/displayname: \'(.*?)\'/', $data, $displayname);
                } else {
                    return ['code' => -1, 'msg' => '登录成功，获取mkey失败！'];
                }
                if ($BDUSS[1] && $STOKEN[1] && $PTOKEN[1]) {
                    return ['code' => 0, 'uid' => self::getUserid($uname[1]), 'user' => $uname[1], 'displayname' => $displayname[1], 'bduss' => $BDUSS[1], 'ptoken' => $PTOKEN[1], 'stoken' => $STOKEN[1]];
                } else {
                    return ['code' => -1, 'msg' => '登录成功，回调百度失败！'];
                }
            } else {
                return ['code' => -1, 'msg' => $ret];
            }
        } else if ($ret) {
            return ['code' => -1, 'msg' => $ret];
        } else {
            return ['code' => 1];
        }
    }

    private static function getUserid($uname)
    {
        $ua = 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36';
        $data = self::get_curl('http://tieba.baidu.com/home/get/panel?ie=utf-8&un=' . urlencode($uname), 0, 0, 0, 0, $ua);
        $arr = json_decode($data, true);
        $userid = $arr['data']['id'];
        return $userid;
    }

    /**
     * 获取QQ登录二维码
     * @return array
     */
    public static function getQqQrCode()
    {
        $url = 'https://ssl.ptlogin2.qq.com/ptqrshow?appid=716027609&e=2&l=M&s=4&d=72&v=4&t=0.2616844' . time() . '&daid=383&pt_3rd_aid=100312028';
        // $arr = self::get_curl($url, 0, 0, $cookie, 1, 0, 0, 1);
        $arr = self::get_curl($url, false, false, false, true, false, false, true);
        preg_match('/qrsig=(.*?);/', $arr['header'], $match);
        if ($qrsig = $match[1]) {
            return [
                "saveOK" => 0,
                "qrsig" => $qrsig,
                "data" => base64_encode($arr['body']),
            ];
        } else {
            return [
                "saveOK" => 1,
                "msg" => "二维码获取失败",
            ];
        }
    }

    /**
     * 二维码登录
     * @param string $qrsig
     * @throws BadRequestException
     * @throws Exception
     * @throws InternalServerErrorException
     */
    public static function qrLogin(string $qrsig = '')
    {
        if (empty($qrsig)) {
            throw new BadRequestException(T('qrsig不能为空'));
        }
        $url = 'https://ssl.ptlogin2.qq.com/ptqrlogin?u1=https%3A%2F%2Fgraph.qq.com%2Foauth2.0%2Flogin_jump&ptqrtoken=' . self::getqrtoken($qrsig) . '&ptredirect=0&h=1&t=1&g=1&from_ui=1&ptlang=2052&action=1-0-' . time() . '000&js_ver=10289&js_type=1&login_sig=fCmEYUeoOds1DTeFIFt2IpGUVa471vZXwy6vQlhx2bOL1CnNRtnCe8J0kv9fTQ1Y&pt_uistyle=40&aid=716027609&daid=383&pt_3rd_aid=100312028&';
        $ret = self::get_curl($url, 0, 'https://xui.ptlogin2.qq.com/cgi-bin/xlogin', 'qrsig=' . $qrsig . '; ', 1);
        if (preg_match("/ptuiCB\('(.*?)'\)/", $ret, $arr)) {
            $r = explode("','", str_replace("', '", "','", $arr[1]));
            if ($r[0] == 0) {
                // $uin = self::getuin($uin[1]);
                $data = self::get_curl($r[2], 0, 'https://xui.ptlogin2.qq.com/cgi-bin/xlogin', 0, 1);
                if ($data) {
                    $cookie = '';
                    preg_match_all('/Set-Cookie: (.*);/iU', $data, $matchs);
                    foreach ($matchs[1] as $val) {
                        if (substr($val, -1) == '=') continue;
                        $cookie .= $val . '; ';
                    }
                    preg_match('/p_skey=(.*?);/', $cookie, $pskey);
                    $cookie = substr($cookie, 0, -2);
                    $data = self::get_curl('https://passport.baidu.com/phoenix/account/startlogin?type=15&tpl=pp&u=https%3A%2F%2Fpassport.baidu.com%2F&display=popup&act=optional', 0, 0, 0, 1);
                    preg_match('/mkey=(.*?);/', $data, $mkey);
                    if ($mkey = $mkey[1]) {
                        $url = 'https://graph.qq.com/oauth2.0/authorize';
                        $post = 'response_type=code&client_id=100312028&redirect_uri=https%3A%2F%2Fpassport.baidu.com%2Fphoenix%2Faccount%2Fafterauth%3Fmkey%3D' . $mkey . '&scope=get_user_info%2Cadd_share%2Cget_other_info%2Cget_fanslist%2Cget_idollist%2Cadd_idol%2Cget_simple_userinfo&state=' . time() . '&switch=&from_ptlogin=1&src=1&update_auth=1&openapi=80901010&g_tk=' . self::getGTK($pskey[1]) . '&auth_time=' . time() . '928&ui=D693AB27-C4CD-4C11-A090-A3EFE7C218EC';
                        $data = self::get_curl($url, $post, false, $cookie, true);
                        preg_match("/Location: (.*?)\r\n/", $data, $match);
                        if ($match[1]) {
                            $data = self::get_curl($match[1], 0, 0, 'mkey=' . $mkey . ';', 1);
                            preg_match('/BDUSS=(.*?);/', $data, $bduss);
                            preg_match('/STOKEN=(.*?);/', $data, $stoken);
                            preg_match('/PTOKEN=(.*?);/', $data, $ptoken);
                            preg_match('/passport_uname: \'(.*?)\'/', $data, $uname);
                            preg_match('/displayname: \'(.*?)\'/', $data, $displayname);
                        } else {
                            // exit('ptuiCB("6","","登录成功，回调百度失败！");');
                            throw new Exception(T("登录成功，回调百度失败！"), 6);
                        }
                    } else {
                        // exit('ptuiCB("6","","登录成功，获取mkey失败！");');
                        throw new Exception(T("登录成功，获取mkey失败！", 6));
                    }
                }
                if ($bduss[1] && $stoken[1] && $ptoken[1]) {
                    // exit('ptuiCB("0","' . self::getUserid($uname[1]) . '","' . $uname[1] . '","' . $displayname[1] . '","' . $bduss[1] . '","' . $stoken[1] . '","' . $ptoken[1] . '");');
                    BaiDuId::add($displayname[1], $bduss[1]);
                    DI()->response->setMsg(T('登录成功'));
                } else {
                    // exit('ptuiCB("6","","登录成功，获取相关信息失败！");');
                    throw new Exception(T("登录成功，获取相关信息失败！"), 6);
                }
            } else if ($r[0] == 65) {
                // exit('ptuiCB("1","","二维码已失效。");');
                throw new Exception(T("二维码已失效。"), 1);
            } else if ($r[0] == 66) {
                // exit('ptuiCB("2","","二维码未失效。");');
                throw new Exception(T("二维码未失效。"), 2);
            } else if ($r[0] == 67) {
                // exit('ptuiCB("3","","正在验证二维码。");');
                throw new Exception(T("正在验证二维码。"), 3);
            } else {
                // exit('ptuiCB("6","","' . str_replace('"', '\'', $r[4]) . '");');
                throw new Exception(T(str_replace('"', '\'', $r[4])), 6);
            }
        } else {
            // exit('ptuiCB("6","","' . $ret . '");');
            throw new Exception(T($ret), 6);
        }
    }

    private static function getqrtoken($qrsig)
    {
        $len = strlen($qrsig);
        $hash = 0;
        for ($i = 0; $i < $len; $i++) {
            $hash += (($hash << 5) & 2147483647) + ord($qrsig[$i]) & 2147483647;
            $hash &= 2147483647;
        }
        return $hash & 2147483647;
    }

    private static function getGTK($skey)
    {
        $len = strlen($skey);
        $hash = 5381;
        for ($i = 0; $i < $len; $i++) {
            $hash += ($hash << 5 & 2147483647) + ord($skey[$i]) & 2147483647;
            $hash &= 2147483647;
        }
        return $hash & 2147483647;
    }

    /**
     * @param $image
     * @return array
     * @throws InternalServerErrorException
     */
    public static function getQqLoginUrl($image)
    {
        $data = DI()->curl->post('http://api.cccyun.cc/api/qrcode_noauth.php', ['image' => urlencode($image)]);
        $arr = json_decode($data, true);
        if ($arr['code'] == 1) {
            return [
                "code" => 0,
                "msg" => "succ",
                "url" => $arr['url'],
            ];
        } else if (array_key_exists('msg', $arr)) {
            throw new InternalServerErrorException(T($arr['msg']));
        } else {
            throw new InternalServerErrorException(T($data));
        }
    }

    private static function getuin($uin)
    {
        for ($i = 0; $i < strlen($uin); $i++) {
            if ($uin[$i] == 'o' || $uin[$i] == '0') {
                continue;
            } else {
                break;
            }
        }
        return substr($uin, $i);
    }
}
