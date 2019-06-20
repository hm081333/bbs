<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2018-11-26
 * Time: 11:14:57
 */

namespace Common\Domain;

/**
 * 贴吧 领域层
 * TieBa
 * @package Common\Domain
 * @author  LYi-Ho 2018-11-26 11:14:57
 */
class TieBa
{
    use Common;

    /**
     * 忽略签到
     * @param $tieba_id
     * @param $no
     * @return bool
     * @throws \Library\Exception\InternalServerErrorException
     */
    public static function noSignTieba($tieba_id, $no)
    {
        $result = self::getModel()->update($tieba_id, ['no' => $no]);
        if ($result === false) {
            throw new \Library\Exception\InternalServerErrorException(\PhalApi\T('操作失败'));
        }
        return true;
    }

    /**
     * 添加BDUSS
     * @param $bduss
     * @throws \Library\Exception\BadRequestException
     * @throws \Library\Exception\InternalServerErrorException
     */
    public static function addBduss($bduss)
    {
        // 去除双引号和bduss=
        $bduss = str_replace('"', '', $bduss);
        $bduss = str_ireplace('BDUSS=', '', $bduss);
        $bduss = str_replace(' ', '', $bduss);
        $bduss = \TieBa\sqlAdds($bduss);
        $baidu_name = \TieBa\sqlAdds(self::getBaiduId($bduss));
        if (empty($baidu_name)) {
            throw new \Library\Exception\BadRequestException(\PhalApi\T('您的 BDUSS Cookie 信息有误，请核验后重新绑定'));
        }
        \Common\Domain\BaiDuId::add($baidu_name, $bduss);
    }

    /**
     * 获取一个bduss对应的百度用户名
     * @param string $bduss BDUSS
     * @return string|bool 百度用户名，失败返回FALSE
     */
    public static function getBaiduId($bduss)
    {
        $url = 'http://wapp.baidu.com/';
        self::DI()->curl->setCookie(['BDUSS' => $bduss, 'BAIDUID' => strtoupper(md5(NOW_TIME))]);
        $data = self::DI()->curl->get($url);
        return urldecode(\TieBa\textMiddle($data, 'i?un=', '">'));
    }

    /**
     * 扫描指定用户的所有贴吧并储存--用于一键刷新
     * @param string $user_id UserID，如果留空，表示当前用户的UID
     * @throws \Library\Exception\BadRequestException
     */
    public static function scanTiebaByUser($user_id = '')
    {
        set_time_limit(0);
        $baiduid_model = self::getModel('BaiDuId');
        if (empty($user_id)) {
            $user = \Common\Domain\User::getCurrentUser(true);// 获取登录状态
            $user_id = $user['id'];
        }
        $bx = $baiduid_model->getListByWhere(['user_id' => $user_id]);
        foreach ($bx as $by) {
            $upid = $by['id'];
            $bduss[$upid] = $by['bduss'];
        }
        foreach ($bduss as $pid => $ubduss) {
            self::scanTiebaByPid($pid);
        }
    }

    /**
     * 扫描指定PID的所有贴吧
     * @param string $pid PID
     */
    public static function scanTiebaByPid($pid)
    {
        set_time_limit(0); // 不超时
        $baiduid_model = self::getModel('BaiDuId');
        $cma = $baiduid_model->get($pid);
        $tieba_model = self::getModel();
        $user_id = $cma['user_id'];
        $bduss = $cma['bduss'];
        $bname = $cma['name'];
        $pid = $cma['id'];
        unset($cma);
        $bid = self::getUserid($bname);
        $pn = 1;
        $a = 0;
        while (true) {
            if (empty($bid)) {
                break;
            }
            $rc = self::getTieba($bid, $bduss, $pn);
            $ngf = isset($rc["forum_list"]["non-gconforum"]) ? $rc["forum_list"]["non-gconforum"] : [];
            if (!empty($rc['forum_list']['gconforum'])) {
                foreach ($rc['forum_list']['gconforum'] as $v) {
                    $ngf[] = $v;
                }
            }
            if (!empty($ngf) && is_array($ngf)) {
                $refresh_time = $rc['time'];
                foreach ($ngf as $v) {
                    $vn = addslashes(htmlspecialchars($v['name']));
                    $ist = $tieba_model->getCount(['baidu_id' => $pid, 'tieba' => $vn]);
                    if ($ist == 0) {
                        $a++;
                        $tieba_model->insert(['baidu_id' => $pid, 'fid' => $v['id'], 'user_id' => $user_id, 'tieba' => $vn, 'refresh_time' => $refresh_time]);
                    }
                }
                if ($a > 0) {
                    $baiduid_model->update($pid, ['refresh_time' => $refresh_time]);
                }
            }
            if ((count($ngf) < 1)) {
                break;
            }
            $pn++;
        }
    }

    /**
     * 获取贴吧用户id
     * 获取指定pid用户userid--根据贴吧用户名找
     */
    private static function getUserid($name)
    {
        $url = "http://tieba.baidu.com/home/get/panel?ie=utf-8&un={$name}";
        $ur = self::DI()->curl->get($url);
        $ur = json_decode($ur, true);
        $userid = $ur['data']['id'];
        return $userid;
    }

    /**
     * 获取指定pid
     */
    public static function getTieba($userid, $bduss, $pn)
    {
        $head = [];
        $head[] = 'Content-Type: application/x-www-form-urlencoded';
        $head[] = 'User-Agent: Mozilla/5.0 (SymbianOS/9.3; Series60/3.2 NokiaE72-1/021.021; Profile/MIDP-2.1 Configuration/CLDC-1.1 ) AppleWebKit/525 (KHTML, like Gecko) Version/3.0 BrowserNG/7.1.16352';
        $url = 'http://c.tieba.baidu.com/c/f/forum/like';
        $data = [
            '_client_id' => 'wappc_' . NOW_TIME . '_' . '258',
            '_client_type' => 2,
            '_client_version' => '6.5.8',
            '_phone_imei' => '357143042411618',
            'from' => 'baidu_appstore',
            'is_guest' => 1,
            'model' => 'H60-L01',
            'page_no' => $pn,
            'page_size' => 200,
            'timestamp' => NOW_TIME . '903',
            'uid' => $userid,
        ];
        $sign_str = '';
        foreach ($data as $k => $v) {
            $sign_str .= $k . '=' . $v;
        }
        $sign = strtoupper(md5($sign_str . 'tiebaclient!!!'));
        $data['sign'] = $sign;
        self::DI()->curl->setHeader($head);
        self::DI()->curl->setCookie(['BDUSS' => $bduss]);
        self::DI()->curl->setOption([CURLOPT_SSL_VERIFYPEER => false, CURLOPT_FOLLOWLOCATION => true]);
        $rt = self::DI()->curl->post($url, $data);
        $rt = json_decode($rt, true);
        return $rt;
    }

    /**
     * 得到贴吧 FID
     * @param string $kw 贴吧名
     * @return string FID
     */
    public static function getFid($kw)
    {
        $url = 'http://tieba.baidu.com/mo/m?kw=' . urlencode($kw);
        self::DI()->curl->setHeader(['User-Agent: fuck phone', 'Referer: http://wapp.baidu.com/', 'Content-Type: application/x-www-form-urlencoded', 'Cookie:BAIDUID=' . strtoupper(md5(NOW_TIME))]);
        $s = self::DI()->curl->get($url);
        $x = \TieBa\easy_match('<input type="hidden" name="fid" value="*"/>', $s);
        if (isset($x[1])) {
            return $x[1];
        } else {
            return false;
        }
    }

    /**
     * 执行全部贴吧用户的签到任务
     */
    public static function doRetryAll()
    {
        set_time_limit(0);
        //处理所有签到出错的贴吧
        $tieba_model = self::getModel();
        $where = [];
        $where['no = ?'] = 0; // 不忽略签到
        $where['status != ?'] = 0; // 签到状态不为0==签到出错
        $total_sign_tieba = $tieba_model->getCount($where); // 该条件下所有贴吧数量
        $limit = 100; // 100条100条循环拿
        $count = ceil($total_sign_tieba / $limit); // 循环100条的次数
        for ($i = 1; $i <= $count; $i++) {
            $tieba_list = $tieba_model->queryRows("
            SELECT
                `t`.`tieba`,
                `t`.`id`,
                `t`.`baidu_id`,
                `t`.`fid`,
                `bid`.`bduss` 
            FROM
                `ly_tieba` AS `t`
                LEFT JOIN `ly_baiduid` AS `bid` ON `t`.`baidu_id` = `bid`.`id` 
            WHERE
                `t`.`no` = ? 
                AND `t`.`status` != ? 
            ORDER BY
                `t`.`id` 
                LIMIT ?,?
            ", [
                0,// 不忽略签到
                0, // 签到状态不为0==签到出错
                0,// 偏移量
                $limit,// 数量
            ]);
            foreach ($tieba_list as $item) {
                self::doSign($item['tieba'], $item['id'], $item['bduss'], $item['fid']);
            }
        }
    }

    /**
     * 获取签到状态
     * @param bool $openid
     * @return array
     * @throws \Library\Exception\BadRequestException
     */
    public static function getSignStatus($openid = false)
    {
        if (empty($openid)) {
            throw new \Library\Exception\BadRequestException(\PhalApi\T('缺少openid'));
        }
        $user_model = self::getModel('User');
        $user = $user_model->getInfo(['open_id=?' => $openid], 'id,user_name');
        if (!$user) {
            throw new \Library\Exception\BadRequestException(\PhalApi\T('没找到该用户'));
        }
        $h = date('G', NOW_TIME);
        if ($h < 11) {
            $greeting = '早上好！';
        } else if ($h < 13) {
            $greeting = '中午好！';
        } else if ($h < 17) {
            $greeting = '下午好！';
        } else {
            $greeting = '晚上好！';
        }
        $day_time = \DateHelper::getDayTime();
        $tieba_model = self::getModel('TieBa');
        $total = $tieba_model->getCount(['user_id=?' => $user['id']]);
        if ($total <= 0) {
            throw new \Library\Exception\BadRequestException(\PhalApi\T('该用户没有贴吧账号'));
        }
        $success_count = $tieba_model->getCount(['user_id=?' => $user['id'], 'no=?' => 0, 'status=?' => 0, 'latest>=?' => $day_time['begin'], 'latest<=?' => $day_time['end']]);//签到成功
        $fail_count = $tieba_model->getCount(['user_id=?' => $user['id'], 'no=?' => 0, 'status>?' => 0, 'latest>=?' => $day_time['begin'], 'latest<=?' => $day_time['end']]);//签到失败
        $no_count = $tieba_model->getCount(['user_id=?' => $user['id'], 'no>?' => 0]);//忽略签到
        $result = [];
        $result['user_name'] = $user['user_name'];
        $result['greeting'] = $greeting;
        $result['tieba_count'] = $total;
        $result['success_count'] = $success_count;
        $result['fail_count'] = $fail_count;
        $result['ignore_count'] = $no_count;

        return $result;
    }

    /**
     * 执行全部贴吧用户的签到任务
     */
    public static function doSignAll()
    {
        set_time_limit(0);
        $day_time = \DateHelper::getDayTime(); // 今天开始的时间和结束的时间的时间戳
        //处理所有未签到的贴吧
        $tieba_model = self::getModel();
        while (true) {
            $tieba_list = $tieba_model->queryRows("
            SELECT
                `t`.`tieba`,
                `t`.`id`,
                `t`.`baidu_id`,
                `t`.`fid`,
                `bid`.`bduss` 
            FROM
                `ly_tieba` AS `t`
                LEFT JOIN `ly_baiduid` AS `bid` ON `t`.`baidu_id` = `bid`.`id` 
            WHERE
                `t`.`no` = ? 
                AND `t`.`latest` < ? 
            ORDER BY
                `t`.`id` 
                LIMIT ?,?
            ", [
                0,// 不忽略签到
                $day_time['begin'], // 今天没有签到
                0,// 偏移量
                100,// 数量 - 100条100条循环拿
            ]);
            if (empty($tieba_list)) {
                break;
            }
            foreach ($tieba_list as $item) {
                self::doSign($item['tieba'], $item['id'], $item['bduss'], $item['fid']);
            }
        }
    }

    /**
     * 执行一个贴吧用户的签到
     */
    public static function doSignByBaiDuId($baidu_id)
    {
        set_time_limit(0);
        $day_time = \DateHelper::getDayTime(); // 今天开始的时间和结束的时间的时间戳
        //处理所有未签到的贴吧
        $tieba_model = self::getModel();
        while (true) {
            $tieba_list = $tieba_model->queryRows("
            SELECT
                `t`.`id`,
                `t`.`tieba`,
                `t`.`fid`,
                `bid`.`bduss`  
            FROM
                `ly_tieba` AS `t`
                LEFT JOIN `ly_baiduid` AS `bid` ON `t`.`baidu_id` = `bid`.`id` 
            WHERE
                `bid`.`id` = ? 
                AND `t`.`no` = ? 
                AND `t`.`latest` < ?
            ORDER BY `t`.`id` 
                LIMIT ?,?
            ",
                [
                    $baidu_id,// 该贴吧用户
                    0,// 不忽略签到
                    $day_time['begin'],// 今天没有签到
                    0,// 从0开始取
                    100,// 100条100条循环拿
                ]
            );
            if (empty($tieba_list)) {
                break;
            }
            foreach ($tieba_list as $item) {
                self::doSign($item['tieba'], $item['id'], $item['bduss'], $item['fid']);
            }
        }
    }

    /**
     * 执行一个会员的签到
     */
    public static function doSignByUserId($user_id)
    {
        set_time_limit(0);
        $day_time = \DateHelper::getDayTime(); // 今天开始的时间和结束的时间的时间戳
        //处理所有未签到的贴吧
        $tieba_model = self::getModel('Tieba');
        while (true) {
            $tieba_list = $tieba_model->queryRows("
            SELECT
                `t`.`id`,
                `t`.`tieba`,
                `t`.`fid`,
                `bid`.`bduss`  
            FROM
                `ly_tieba` AS `t`
                LEFT JOIN `ly_baiduid` AS `bid` ON `t`.`baidu_id` = `bid`.`id` 
            WHERE
                `bid`.`user_id` = ? 
                AND `t`.`no` = ? 
                AND `t`.`latest` < ?
            ORDER BY `t`.`id` 
                LIMIT ?,?
            ",
                [
                    $user_id,// 该会员
                    0,// 不忽略签到
                    $day_time['begin'],// 今天没有签到
                    0,// 从0开始取
                    100,// 100条100条循环拿
                ]
            );
            if (empty($tieba_list)) {
                break;
            }
            foreach ($tieba_list as $item) {
                self::doSign($item['tieba'], $item['id'], $item['bduss'], $item['fid']);
            }
        }
    }

    /**
     * 执行一个贴吧的签到
     * @param $tieba_id
     * @return array|mixed
     * @throws \Library\Exception\BadRequestException
     */
    public static function doSignByTieBaId($tieba_id)
    {
        // $x = self::getModel()->get($tieba_id);
        $tieba_info = self::getModel()->queryRows("
        SELECT
            `t`.`tieba`,
            `t`.`fid`,
            `bid`.`bduss` 
        FROM
            `ly_tieba` AS `t`
            LEFT JOIN `ly_baiduid` AS `bid` ON `t`.`baidu_id` = `bid`.`id` 
        WHERE
            `t`.`id` =?
        ",
            [
                $tieba_id,
            ]
        );
        $tieba_info = $tieba_info[0] ?? [];
        if (empty($tieba_info)) {
            throw new \Library\Exception\BadRequestException(\PhalApi\T('您没有该贴吧'));
        }
        self::doSign($tieba_info['tieba'], $tieba_id, $tieba_info['bduss'], $tieba_info['fid']);
        return self::getInfo($tieba_id);
    }

    /**
     * 对一个贴吧执行完整的签到任务
     * @param $kw
     * @param $id
     * @param $bduss
     * @param $fid
     * @return array|mixed
     */
    public static function doSign($kw, $id, $bduss, $fid)
    {
        $again_error_id = 160002; //重复签到错误代码
        $again_error_id_2 = 1101; //特殊的重复签到错误代码！！！签到过快=已签到
        $again_error_id_3 = 1102; //特殊的重复签到错误代码！！！签到过快=已签到
        $status_succ = false;
        $ck = $bduss;
        $kw = addslashes($kw);
        $tieba_model = self::getModel();

        $update_data = [];

        if (empty($fid)) {
            $fid = self::getFid($kw);//贴吧唯一ID
            $update_data = array_merge($update_data, ['fid' => $fid]);
        }

        //三种签到方式依次尝试
        $tbs = self::getTbs($ck);
        //客户端
        if ($status_succ === false) {
            $r = self::DoSign_Client($kw, $fid, $ck, $tbs);
            $v = json_decode($r, true);
            if ($v != $r && $v != null) {//decode失败时会直接返回原文或NULL
                $time = $v['time'];
                if (empty($v['error_code']) || $v['error_code'] == $again_error_id) {
                    $status_succ = true;
                } else {
                    $error_code = $v['error_code'];
                    $error_msg = $v['error_msg'];
                }
            }
        }

        /*//手机网页
        if ($status_succ === false) {
            $r = self::DoSign_Mobile($kw, $fid, $ck, $tbs);
            $v = json_decode($r, true);
            if ($v != $r && $v != NULL) {//decode失败时会直接返回原文或NULL
                if (empty($v['no']) || $v['no'] == $again_error_id_2 || $v['no'] == $again_error_id_3) {
                    $status_succ = true;
                } else {
                    $error_code = $v['no'];
                    $error_msg = $v['error'];
                }
            }
        }

        //网页---尽量不用
        if ($status_succ === false) {
            if (self::DoSign_Default($kw, $fid, $ck) === true) {
                $status_succ = true;
            }
        }*/

        if ($status_succ === true) {
            $update_data = array_merge($update_data, ['latest' => $time, 'status' => 0, 'last_error' => '']);
        } else {
            $update_data = array_merge($update_data, ['latest' => $time, 'status' => $error_code, 'last_error' => $error_msg]);
        }
        $tieba_model->update($id, $update_data);
        // return self::getInfo($id);
        return true;
    }

    /**
     * 得到BDUSS
     * @param int|string $baidu_id 贴吧用户PID
     */
    /*public static function getCookie($baidu_id)
    {
        if (empty($baidu_id)) {
            return false;
        }
        $baiduid_model = new Model_BaiduId();
        $temp = $baiduid_model->get($baidu_id);
        return $temp['bduss'];
    }*/

    /**
     * 得到TBS
     */
    public static function getTbs($bduss)
    {
        $url = 'http://tieba.baidu.com/dc/common/tbs';
        self::DI()->curl->setHeader(['User-Agent: fuck phone', 'Referer: http://tieba.baidu.com/', 'X-Forwarded-For: 115.28.1.' . mt_rand(1, 255)]);
        self::DI()->curl->setCookie(["BDUSS" => $bduss]);
        $x = self::DI()->curl->get($url);
        $x = json_decode($x, true);
        return $x['tbs'];
    }

    /**
     * 客户端签到
     */
    public static function DoSign_Client($kw, $fid, $ck, $tbs)
    {
        $temp = [
            'BDUSS' => $ck,
            '_client_id' => '03-00-DA-59-05-00-72-96-06-00-01-00-04-00-4C-43-01-00-34-F4-02-00-BC-25-09-00-4E-36',
            '_client_type' => '4',
            '_client_version' => '1.2.1.17',
            '_phone_imei' => '540b43b59d21b7a4824e1fd31b08e9a6',
            'fid' => $fid,
            'kw' => $kw,
            'net_type' => '3',
            'tbs' => $tbs,
        ];
        $x = '';
        foreach ($temp as $k => $v) {
            $x .= $k . '=' . $v;
        }
        $temp['sign'] = strtoupper(md5($x . 'tiebaclient!!!'));
        $url = 'http://c.tieba.baidu.com/c/c/forum/sign';
        self::DI()->curl->setHeader(['Content-Type: application/x-www-form-urlencoded', 'User-Agent: Fucking iPhone/1.0 BadApple/99.1']);
        self::DI()->curl->setCookie(["BDUSS" => $ck]);
        return self::DI()->curl->post($url, $temp);
    }

    /**
     * 手机网页签到
     */
    public static function DoSign_Mobile($kw, $fid, $ck, $tbs)
    {
        $url = 'http://tieba.baidu.com/mo/q/sign?tbs=' . $tbs . '&kw=' . urlencode($kw) . '&is_like=1&fid=' . $fid;
        self::DI()->curl->setHeader(['User-Agent: fuck phone', 'Referer: http://tieba.baidu.com/f?kw=' . $kw, 'Host: tieba.baidu.com', 'X-Forwarded-For: 115.28.1.' . mt_rand(1, 255), 'Origin: http://tieba.baidu.com', 'Connection: Keep-Alive']);
        self::DI()->curl->setCookie(['BDUSS' => $ck]);
        return self::DI()->curl->get($url);
    }

    /**
     * 网页签到
     */
    public static function DoSign_Default($kw, $fid, $ck)
    {
        $url = 'http://tieba.baidu.com/mo/m?kw=' . urlencode($kw) . '&fid=' . $fid;
        self::DI()->curl->setHeader(['User-Agent: fuck phone', 'Referer: http://wapp.baidu.com/', 'Content-Type: application/x-www-form-urlencoded']);
        self::DI()->curl->setCookie(['BDUSS' => $ck]);
        $s = self::DI()->curl->get($url);
        preg_match('/\<td style=\"text-align:right;\"\>\<a href=\"(.*)\"\>签到\<\/a\>\<\/td\>\<\/tr\>/', $s, $s);
        if (isset($s[1])) {
            $url = 'http://tieba.baidu.com' . $s[1];
            self::DI()->curl->setHeader(['Accept: text/html, application/xhtml+xml, */*', 'Accept-Language: zh-Hans-CN,zh-Hans;q=0.8,en-US;q=0.5,en;q=0.3', 'User-Agent: Fucking Phone']);
            self::DI()->curl->setCookie(['BDUSS' => $ck]);
            self::DI()->curl->get($url);
            //临时判断解决方案
            $url = 'http://tieba.baidu.com/mo/m?kw=' . urlencode($kw) . '&fid=' . $fid;
            self::DI()->curl->setHeader(['User-Agent: fuck phone', 'Referer: http://wapp.baidu.com/', 'Content-Type: application/x-www-form-urlencoded']);
            self::DI()->curl->setCookie(['BDUSS' => $ck]);
            $s = self::DI()->curl->get($url);
            //如果找不到这段html则表示没有签到则stripos()返回false，同时is_bool()返回true，最终返回false
            return !is_bool(stripos($s, '<td style="text-align:right;"><span >已签到</span></td>'));
        } else {
            return true;
        }
    }

    /**
     * CURL整合--返回数组
     * @param     $url
     * @param int $post
     * @param int $referer
     * @param int $cookie
     * @param int $header
     * @param int $ua
     * @param int $nobaody
     * @return mixed
     * @throws \Library\Exception\InternalServerErrorException
     */
    private static function get_curl($url, $post = false, $referer = true, $cookie = false, $header = false, $ua = false, $nobaody = false)
    {
        $httpheader = [];
        $httpheader[] = "Accept:application/json";
        $httpheader[] = "Accept-Encoding:gzip,deflate,sdch";
        $httpheader[] = "Accept-Language:zh-CN,zh;q=0.8";
        $httpheader[] = "Connection:close";
        self::DI()->curl->setHeader($httpheader);
        $option = [];
        $option[CURLOPT_SSL_VERIFYPEER] = false;
        $option[CURLOPT_SSL_VERIFYHOST] = false;
        if ($header) {
            $option[CURLOPT_HEADER] = true;
        }
        if ($cookie) {
            $option[CURLOPT_COOKIE] = $cookie;
        }
        if ($referer) {
            $option[CURLOPT_REFERER] = 'https://wappass.baidu.com/';
        }
        if ($ua) {
            $option[CURLOPT_USERAGENT] = $ua;
        } else {
            $option[CURLOPT_USERAGENT] = 'Mozilla/5.0 (Linux; Android 4.4.2; H650 Build/KOT49H) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/30.0.0.0 Mobile Safari/537.36';
            //$option[CURLOPT_USERAGENT] = 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36';
        }
        if ($nobaody) {
            $option[CURLOPT_NOBODY] = 1;
        }
        $option[CURLOPT_ENCODING] = 'gzip';
        self::DI()->curl->setOption($option);
        if ($post) {
            if (is_array($post)) {
                $post = http_build_query($post);
            }
            $ret = self::DI()->curl->post($url, $post);
        } else {
            $ret = self::DI()->curl->get($url);
        }
        if (empty($ret)) {
            throw new \Library\Exception\InternalServerErrorException(\PhalApi\T('连接到百度服务器失败'));
        }
        return $ret;
    }

    /**
     * 获取ServerTime
     * @return array
     * @throws \Library\Exception\InternalServerErrorException
     */
    public static function serverTime()
    {
        $url = 'https://wappass.baidu.com/wp/api/security/antireplaytoken?tpl=tb&v=' . NOW_TIME . '0000';
        $data = self::get_curl($url);
        $arr = json_decode($data, true);
        if ($arr['errno'] == 110000) {
            return ['code' => 0, 'time' => $arr['time']];
        } else {
            return ['code' => -1, 'msg' => $arr['errmsg']];
        }
    }

    /**
     * 获取验证码图片
     * @param $vCodeStr
     * @return mixed
     * @throws \Library\Exception\InternalServerErrorException
     */
    public static function getVCPic($vCodeStr)
    {
        $url = 'https://wappass.baidu.com/cgi-bin/genimage?' . $vCodeStr . '&v=' . NOW_TIME . '0000';
        return self::get_curl($url);
    }

    /**
     * 普通登录操作
     * @param string $time
     * @param string $user
     * @param string $pwd
     * @param string $p
     * @param string $vcode
     * @param string $vcodestr
     * @return array
     * @throws \Library\Exception\InternalServerErrorException
     */
    public static function login(string $time, string $user, string $pwd, string $p, string $vcode = '', string $vcodestr = '')
    {
        self::DI()->response->setMsg(\PhalApi\T('成功'));
        $url = 'https://wappass.baidu.com/wp/api/login?v=' . NOW_TIME . '0000';
        $post = 'username=' . $user . '&code=&password=' . $p . '&verifycode=' . $vcode . '&clientfrom=native&tpl=tb&login_share_strategy=choice&client=android&adapter=3&t=' . NOW_TIME . '0000&act=bind_mobile&loginLink=0&smsLoginLink=1&lPFastRegLink=0&fastRegLink=1&lPlayout=0&loginInitType=0&lang=zh-cn&regLink=1&action=login&loginmerge=1&isphone=0&dialogVerifyCode=&dialogVcodestr=&dialogVcodesign=&gid=660BDF6-30E5-4A83-8EAC-F0B4752E1C4B&vcodestr=' . $vcodestr . '&countrycode=&servertime=' . $time . '&logLoginType=sdk_login&passAppHash=&passAppVersion=';
        $data = self::get_curl($url, $post);
        $arr = json_decode($data, true);
        if (array_key_exists('errInfo', $arr) && $arr['errInfo']['no'] == '0') {
            if (!empty($arr['data']['loginProxy'])) {
                $data = self::get_curl($arr['data']['loginProxy']);
                $arr = json_decode($data, true);
            }
            $data = $arr['data']['xml'];
            preg_match('!<uname>(.*?)</uname>!i', $data, $user);
            preg_match('!<uid>(.*?)</uid>!i', $data, $uid);
            preg_match('!<portrait>(.*?)</portrait>!i', $data, $face);
            preg_match('!<displayname>(.*?)</displayname>!i', $data, $displayname);
            preg_match('!<bduss>(.*?)</bduss>!i', $data, $bduss);
            preg_match('!<ptoken>(.*?)</ptoken>!i', $data, $ptoken);
            preg_match('!<stoken>(.*?)</stoken>!i', $data, $stoken);
            return ['code' => 0, 'uid' => $uid[1], 'user' => $user[1], 'displayname' => $displayname[1], 'face' => $face[1], 'bduss' => $bduss[1], 'ptoken' => $ptoken[1], 'stoken' => $stoken[1]];
        } else if ($arr['errInfo']['no'] == '310006' || $arr['errInfo']['no'] == '500001' || $arr['errInfo']['no'] == '500002') {
            return ['code' => $arr['errInfo']['no'], 'msg' => $arr['errInfo']['msg'], 'vcodestr' => $arr['data']['codeString']];
        } else if ($arr['errInfo']['no'] == '400023') {
            return ['code' => $arr['errInfo']['no'], 'msg' => $arr['errInfo']['msg'], 'type' => $arr['data']['showType'], 'phone' => $arr['data']['phone'], 'email' => $arr['data']['email'], 'lstr' => $arr['data']['lstr'], 'ltoken' => $arr['data']['ltoken']];
        } else if (array_key_exists('errInfo', $arr)) {
            return ['code' => $arr['errInfo']['no'], 'msg' => $arr['errInfo']['msg']];
        } else {
            throw new \Library\Exception\InternalServerErrorException(\PhalApi\T('登录失败，原因未知'));
        }
    }

    /**
     * 登录异常时发送手机/邮件验证码
     * @param $type
     * @param $lstr
     * @param $ltoken
     * @return array
     * @throws \Library\Exception\InternalServerErrorException
     */
    public static function sendCode($type, $lstr, $ltoken)
    {
        $url = 'https://wappass.baidu.com/wp/login/sec?ajax=1&v=' . NOW_TIME . '0000&vcode=&clientfrom=native&tpl=tb&login_share_strategy=choice&client=android&adapter=3&t=' . NOW_TIME . '0000&act=bind_mobile&loginLink=0&smsLoginLink=1&lPFastRegLink=0&fastRegLink=1&lPlayout=0&loginInitType=0&lang=zh-cn&regLink=1&action=login&loginmerge=1&isphone=0&dialogVerifyCode=&dialogVcodestr=&dialogVcodesign=&gid=660BDF6-30E5-4A83-8EAC-F0B4752E1C4B&showtype=' . $type . '&lstr=' . rawurlencode($lstr) . '&ltoken=' . $ltoken;
        $data = self::get_curl($url, 0);
        $arr = json_decode($data, true);
        if (array_key_exists('errInfo', $arr) && $arr['errInfo']['no'] == '0') {
            return ['code' => 0];
        } else if (array_key_exists('errInfo', $arr)) {
            return ['code' => $arr['errInfo']['no'], 'msg' => $arr['errInfo']['msg']];
        } else {
            throw new \Library\Exception\InternalServerErrorException(\PhalApi\T('发生验证码失败，原因未知'));
        }
    }

    /**
     * 登录异常时登录操作
     * @param string $type
     * @param string $lstr
     * @param string $ltoken
     * @param string $vcode
     * @return array
     * @throws \Library\Exception\BadRequestException
     * @throws \Library\Exception\InternalServerErrorException
     */
    public static function login2(string $type, string $lstr, string $ltoken, string $vcode)
    {
        self::DI()->response->setMsg(\PhalApi\T('成功'));
        $url = 'https://wappass.baidu.com/wp/login/sec?type=2&v=' . NOW_TIME . '0000';
        $post = [
            'vcode' => $vcode,
            'clientfrom' => 'native',
            'tpl' => 'tb',
            'login_share_strategy' => 'choice',
            'client' => 'android',
            'adapter' => '3',
            't' => NOW_TIME . '0000',
            'act' => 'bind_mobile',
            'loginLink' => '0',
            'smsLoginLink' => '1',
            'lPFastRegLink' => '0',
            'fastRegLink' => '1',
            'lPlayout' => '0',
            'loginInitType' => '0',
            'lang' => 'zh-cn',
            'regLink' => '1',
            'action' => 'login',
            'loginmerge' => '1',
            'isphone' => '0',
            'dialogVerifyCode' => '',
            'dialogVcodestr' => '',
            'dialogVcodesign' => '',
            'gid' => '660BDF6-30E5-4A83-8EAC-F0B4752E1C4B',
            'showtype' => $type,
            'lstr' => rawurlencode($lstr),
            'ltoken' => $ltoken,
        ];
        // $post = 'vcode=' . $vcode . '&clientfrom=native&tpl=tb&login_share_strategy=choice&client=android&adapter=3&t=' . NOW_TIME . '0000&act=bind_mobile&loginLink=0&smsLoginLink=1&lPFastRegLink=0&fastRegLink=1&lPlayout=0&loginInitType=0&lang=zh-cn&regLink=1&action=login&loginmerge=1&isphone=0&dialogVerifyCode=&dialogVcodestr=&dialogVcodesign=&gid=660BDF6-30E5-4A83-8EAC-F0B4752E1C4B&showtype=' . $type . '&lstr=' . rawurlencode($lstr) . '&ltoken=' . $ltoken;
        $data = self::get_curl($url, $post);
        $arr = json_decode($data, true);
        if (array_key_exists('errInfo', $arr) && $arr['errInfo']['no'] == '0') {
            if (!empty($arr['data']['loginProxy'])) {
                $data = self::get_curl($arr['data']['loginProxy'], 0);
                $arr = json_decode($data, true);
            }
            $data = $arr['data']['xml'];
            // preg_match('!<uname>(.*?)</uname>!i', $data, $user);
            // preg_match('!<uid>(.*?)</uid>!i', $data, $uid);
            // preg_match('!<portrait>(.*?)</portrait>!i', $data, $face);
            preg_match('!<displayname>(.*?)</displayname>!i', $data, $displayname);
            preg_match('!<bduss>(.*?)</bduss>!i', $data, $bduss);
            // preg_match('!<ptoken>(.*?)</ptoken>!i', $data, $ptoken);
            // preg_match('!<stoken>(.*?)</stoken>!i', $data, $stoken);
            // return ['code' => 0, 'uid' => $uid[1], 'user' => $user[1], 'displayname' => $displayname[1], 'face' => $face[1], 'bduss' => $bduss[1], 'ptoken' => $ptoken[1], 'stoken' => $stoken[1]];
            \Common\Domain\BaiDuId::add($displayname[1], $bduss[1]);
        } else if (array_key_exists('errInfo', $arr)) {
            return ['code' => $arr['errInfo']['no'], 'msg' => $arr['errInfo']['msg']];
        } else {
            throw new \Library\Exception\InternalServerErrorException(\PhalApi\T('登录失败，原因未知'));
        }
    }

    /**
     * 检测是否需要验证码
     * @param $user
     * @return array
     * @throws \Library\Exception\BadRequestException
     * @throws \Library\Exception\InternalServerErrorException
     */
    public static function checkVC($user)
    {
        if (empty($user)) {
            throw new \Library\Exception\BadRequestException(\PhalApi\T('请先输入用户名'));
        }
        $url = 'https://wappass.baidu.com/wp/api/login/check?tt=' . NOW_TIME . '9117&username=' . $user . '&countrycode=&clientfrom=wap&sub_source=leadsetpwd&tpl=tb';
        $data = self::get_curl($url);
        $arr = json_decode($data, true);
        if ($arr['errInfo'] && $arr['errInfo']['no'] == '0' && empty($arr['data']['codeString'])) {
            return ['code' => 0];
        } else if ($arr['errInfo'] && $arr['errInfo']['no'] == '0') {
            return ['code' => 1, 'vcodestr' => $arr['data']['codeString']];
        } else {
            return ['code' => $arr['errInfo']['no'], 'msg' => $arr['errInfo']['msg']];
        }
    }

    /**
     * 手机验证码登录，获取手机号是否存在
     * @param $phone
     * @return array
     * @throws \Library\Exception\BadRequestException
     * @throws \Library\Exception\InternalServerErrorException
     */
    public static function getPhone($phone)
    {
        if (empty($phone)) {
            throw new \Library\Exception\BadRequestException(\PhalApi\T('请先输入手机号'));
        }
        if (strlen($phone) != 11) {
            throw new \Library\Exception\BadRequestException(\PhalApi\T('请输入正确的手机号'));
        }
        $phone2 = '';
        for ($i = 0; $i < 11; $i++) {
            $phone2 .= $phone[$i];
            if ($i == 2 || $i == 6) $phone2 .= '+';
        }
        $url = 'https://wappass.baidu.com/wp/api/security/getphonestatus?v=' . NOW_TIME . '0000';
        $post = [
            'mobilenum' => $phone2,
            'clientfrom' => 'native',
            'tpl' => 'tb',
            'login_share_strategy' => 'choice',
            'client' => 'android',
            'adapter' => 3,
            't' => NOW_TIME . '0000',
            'act' => 'bind_mobile',
            'loginLink' => 0,
            'smsLoginLink' => 1,
            'lPFastRegLink' => 0,
            'fastRegLink' => 1,
            'lPlayout' => 0,
            'lang' => 'zh-cn',
            'regLink' => 1,
            'action' => 'login',
            'loginmerge' => 1,
            'isphone' => 0,
            'dialogVerifyCode' => '',
            'dialogVcodestr' => '',
            'dialogVcodesign' => '',
            'gid' => 'E528690-4ADF-47A5-BA87-1FD76D2583EA',
            'agreement' => 1,
            'vcodesign' => '',
            'vcodestr' => '',
            'sms' => 1,
            'username' => $phone,
            'countrycode' => '',
        ];
        // $post = 'mobilenum=' . $phone2 . '&clientfrom=native&tpl=tb&login_share_strategy=choice&client=android&adapter=3&t=' . SERVER_TIME . '0000&act=bind_mobile&loginLink=0&smsLoginLink=1&lPFastRegLink=0&fastRegLink=1&lPlayout=0&lang=zh-cn&regLink=1&action=login&loginmerge=1&isphone=0&dialogVerifyCode=&dialogVcodestr=&dialogVcodesign=&gid=E528690-4ADF-47A5-BA87-1FD76D2583EA&agreement=1&vcodesign=&vcodestr=&sms=1&username=' . $phone . '&countrycode=';
        $data = self::get_curl($url, $post);
        $arr = json_decode($data, true);
        if ($arr['errInfo'] && $arr['errInfo']['no'] == '0') {
            return ['code' => 0, 'msg' => $arr['errInfo']['msg']];
        } else {
            return ['code' => $arr['errInfo']['no'], 'msg' => $arr['errInfo']['msg']];
        }
    }

    /**
     * 手机验证码登录，发送验证码
     * @param        $phone
     * @param string $vcode
     * @param string $vcodestr
     * @param string $vcodesign
     * @return array
     * @throws \Library\Exception\BadRequestException
     * @throws \Library\Exception\InternalServerErrorException
     */
    public static function sendSms($phone, $vcode = '', $vcodestr = '', $vcodesign = '')
    {
        if (empty($phone)) {
            throw new \Library\Exception\BadRequestException(\PhalApi\T('请先输入手机号'));
        }
        if (strlen($phone) != 11) {
            throw new \Library\Exception\BadRequestException(\PhalApi\T('请输入正确的手机号'));
        }
        $url = 'https://wappass.baidu.com/wp/api/login/sms?v=' . NOW_TIME . '0000';
        $post = [
            'username' => $phone,
            'tpl' => 'tb',
            'clientfrom' => 'native',
            'countrycode' => '',
            'gid' => 'E528690-4ADF-47A5-BA87-1FD76D2583EA',
            'dialogVerifyCode' => $vcode,
            'vcodesign' => $vcodesign,
            'vcodestr' => $vcodestr,
        ];
        // $post = 'username=' . $phone . '&tpl=tb&clientfrom=native&countrycode=&gid=E528690-4ADF-47A5-BA87-1FD76D2583EA&dialogVerifyCode=' . $vcode . '&vcodesign=' . $vcodesign . '&vcodestr=' . $vcodestr;
        $data = self::get_curl($url, $post);
        $arr = json_decode($data, true);
        if ($arr['errInfo'] && $arr['errInfo']['no'] == '0') {
            return ['code' => 0, 'msg' => $arr['errInfo']['msg']];
        } else if ($arr['errInfo']['no'] == '50020') {
            return ['code' => $arr['errInfo']['no'], 'msg' => $arr['errInfo']['msg'], 'vcodestr' => $arr['data']['vcodestr'], 'vcodesign' => $arr['data']['vcodesign']];
        } else {
            return ['code' => $arr['errInfo']['no'], 'msg' => $arr['errInfo']['msg']];
        }
    }

    /**
     * 手机验证码登录操作
     * @param $phone
     * @param $smsvc
     * @return array
     * @throws \Library\Exception\BadRequestException
     * @throws \Library\Exception\InternalServerErrorException
     */
    public static function login3($phone, $smsvc)
    {
        if (empty($phone)) {
            throw new \Library\Exception\BadRequestException(\PhalApi\T('手机号不能为空'));
        }
        if (strlen($phone) != 11) {
            throw new \Library\Exception\BadRequestException(\PhalApi\T('请输入正确的手机号'));
        }
        if (empty($smsvc)) {
            throw new \Library\Exception\BadRequestException(\PhalApi\T('验证码不能为空'));
        }

        $url = 'https://wappass.baidu.com/wp/api/login?v=' . NOW_TIME . '0000';
        $post = [
            'smsvc' => $smsvc,
            'clientfrom' => 'native',
            'tpl' => 'tb',
            'login_share_strategy' => 'choice',
            'client' => 'android',
            'adapter' => '3',
            't' => NOW_TIME . '0000',
            'act' => 'bind_mobile',
            'loginLink' => '0',
            'smsLoginLink' => '1',
            'lPFastRegLink' => '0',
            'fastRegLink' => '1',
            'lPlayout' => '0',
            'lang' => 'zh-cn',
            'regLink' => '1',
            'action' => 'login',
            'loginmerge' => '',
            'isphone' => '0',
            'dialogVerifyCode' => '',
            'dialogVcodestr' => '',
            'dialogVcodesign' => '',
            'gid' => 'E528690-4ADF-47A5-BA87-1FD76D2583EA',
            'agreement' => '1',
            'vcodesign' => '',
            'vcodestr' => '',
            'smsverify' => '1',
            'sms' => '1',
            'mobilenum' => $phone,
            'username' => $phone,
            'countrycode' => '',
            'passAppHash' => '',
            'passAppVersion' => '',
        ];

        // $post = 'smsvc=' . $smsvc . '&clientfrom=native&tpl=tb&login_share_strategy=choice&client=android&adapter=3&t=' . SERVER_TIME . '0000&act=bind_mobile&loginLink=0&smsLoginLink=1&lPFastRegLink=0&fastRegLink=1&lPlayout=0&lang=zh-cn&regLink=1&action=login&loginmerge=&isphone=0&dialogVerifyCode=&dialogVcodestr=&dialogVcodesign=&gid=E528690-4ADF-47A5-BA87-1FD76D2583EA&agreement=1&vcodesign=&vcodestr=&smsverify=1&sms=1&mobilenum=' . $phone . '&username=' . $phone . '&countrycode=&passAppHash=&passAppVersion=';

        $data = self::get_curl($url, $post);
        $arr = json_decode($data, true);
        if (array_key_exists('errInfo', $arr) && $arr['errInfo']['no'] == '0') {
            if (!empty($arr['data']['loginProxy'])) {
                $data = self::get_curl($arr['data']['loginProxy']);
                $arr = json_decode($data, true);
            }
            $data = $arr['data']['xml'];
            // preg_match('!<uname>(.*?)</uname>!i', $data, $user);
            // preg_match('!<uid>(.*?)</uid>!i', $data, $uid);
            // preg_match('!<portrait>(.*?)</portrait>!i', $data, $face);
            preg_match('!<displayname>(.*?)</displayname>!i', $data, $displayname);
            preg_match('!<bduss>(.*?)</bduss>!i', $data, $bduss);
            // preg_match('!<ptoken>(.*?)</ptoken>!i', $data, $ptoken);
            // preg_match('!<stoken>(.*?)</stoken>!i', $data, $stoken);
            // return ['code' => 0, 'uid' => $uid[1], 'user' => $user[1], 'displayname' => $displayname[1], 'face' => $face[1], 'bduss' => $bduss[1], 'ptoken' => $ptoken[1], 'stoken' => $stoken[1]];
            \Common\Domain\BaiDuId::add($displayname[1], $bduss[1]);
        } else if (array_key_exists('errInfo', $arr)) {
            return ['code' => $arr['errInfo']['no'], 'msg' => $arr['errInfo']['msg']];
        } else {
            throw new \Library\Exception\InternalServerErrorException(\PhalApi\T('登录失败，原因未知'));
        }
    }

    /**
     * 获取扫码登录二维码
     * @return array
     * @throws \Library\Exception\InternalServerErrorException
     */
    public static function getQRCode()
    {
        $url = 'https://passport.baidu.com/v2/api/getqrcode?lp=pc&gid=07D9D20-91EB-43D8-8553-16A98A0B24AA&apiver=v3&tt=' . NOW_TIME . '0000&callback=callback';
        // $data = self::get_curl($url, FALSE, 'https://passport.baidu.com/v2/?login');
        $data = self::get_curl($url);
        preg_match('/callback\((.*?)\)/', $data, $match);
        $arr = json_decode($match[1], true);
        if (array_key_exists('errno', $arr) && $arr['errno'] == 0) {
            return ['code' => 0, 'imgurl' => $arr['imgurl'], 'sign' => $arr['sign'], 'link' => 'https://wappass.baidu.com/wp/?qrlogin&t=' . NOW_TIME . '&error=0&sign=' . $arr['sign'] . '&cmd=login&lp=pc&tpl=&uaonly='];
        } else {
            return ['code' => $arr['errno'], 'msg' => '获取二维码失败'];
        }
    }

    /**
     * 扫码登录操作
     * @param $sign
     * @throws \Library\Exception\BadRequestException
     * @throws \Library\Exception\InternalServerErrorException
     */
    public static function qrLogin($sign)
    {
        $url = 'https://passport.baidu.com/channel/unicast?channel_id=' . $sign . '&tpl=pp&gid=07D9D20-91EB-43D8-8553-16A98A0B24AA&apiver=v3&tt=' . NOW_TIME . '0000&callback=callback';
        $data = self::get_curl($url);
        preg_match('/callback\((.*?)\)/', $data, $match);
        $arr = json_decode($match[1], true);
        if (array_key_exists('errno', $arr) && $arr['errno'] == 0) {
            $arr = json_decode($arr['channel_v'], true);
            $data = self::get_curl('https://passport.baidu.com/v2/api/bdusslogin?bduss=' . $arr['v'] . '&u=https%3A%2F%2Fpassport.baidu.com%2F&qrcode=1&tpl=pp&apiver=v3&tt=' . NOW_TIME . '0000&callback=callback', false, true, false, true);
            preg_match('/callback\((.*?)\)/', $data, $match);
            $arr = json_decode($match[1], true);
            if (array_key_exists('errInfo', $arr) && $arr['errInfo']['no'] == '0') {
                $data = str_replace('=deleted', '', $data);
                preg_match('!BDUSS=(.*?);!i', $data, $bduss);

                \Common\Domain\BaiDuId::add($arr['data']['displayname'], $bduss[1]);

                // preg_match('!PTOKEN=(.*?);!i', $data, $ptoken);
                // preg_match('!STOKEN=(.*?);!i', $data, $stoken);
                // $userid = self::getUserid($arr['data']['userName']);
                // return ['code' => 0, 'uid' => $userid, 'user' => $arr['data']['userName'], 'displayname' => $arr['data']['displayname'], 'mail' => $arr['data']['mail'], 'phone' => $arr['data']['phoneNumber'], 'bduss' => $bduss[1], 'ptoken' => $ptoken[1], 'stoken' => $stoken[1]];
            } else if (array_key_exists('errInfo', $arr)) {
                throw new \Library\Exception\InternalServerErrorException(\PhalApi\T($arr['errInfo']['msg']));
                // return ['code' => $arr['errInfo']['no'], 'msg' => $arr['errInfo']['msg']];
            } else {
                throw new \Library\Exception\InternalServerErrorException(\PhalApi\T('登录失败，原因未知'));
                // return ['code' => '-1', 'msg' => '登录失败，原因未知'];
            }
        } else if (array_key_exists('errno', $arr)) {
            throw new \Library\Exception\InternalServerErrorException(\PhalApi\T('未检测到登录状态'));
            // return ['code' => $arr['errno']];
        } else {
            throw new \Library\Exception\InternalServerErrorException(\PhalApi\T('登录失败，原因未知'));
            // return ['code' => '-1', 'msg' => '登录失败，原因未知'];
        }
    }

}
