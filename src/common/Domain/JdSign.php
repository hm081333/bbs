<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2018-11-26
 * Time: 11:14:57
 */

namespace Common\Domain;


/**
 * 京东签到 领域层
 * JdSign
 * @package Common\Domain
 * @author  LYi-Ho 2020-01-14 15:53:11
 */
class JdSign
{
    use Common;

    static $user_cookie = [];

    /**
     * 设置京东签到项目
     * @param int   $jd_user_id
     * @param array $data
     * @throws \Library\Exception\BadRequestException
     * @throws \Library\Exception\Exception
     */
    public static function doInfo(int $jd_user_id, array $data)
    {
        self::DI()->response->setMsg(\PhalApi\T('设置成功'));

        $user = \Common\Domain\User::getCurrentUser(true);

        $jd_user_id = intval($jd_user_id);
        $user_id = intval($user['id']);

        $modelJdSign = self::getModel();
        $modelJdUser = self::getModel('jdUser');
        $jd_user_info = $modelJdUser->getInfo([
            'id' => $jd_user_id,
            'user_id' => $user_id,
        ]);
        if (!$jd_user_info) {
            throw new \Library\Exception\Exception(\PhalApi\T('找不到该京东用户'));
        }
        $update_all_where = [
            'user_id' => $user_id,
            'jd_user_id' => $jd_user_info['id'],
            'status' => 1,
        ];
        $update_all_data = [
            'status' => 0,
            'edit_time' => NOW_TIME,
        ];
        if (!empty($data)) {
            foreach ($data as $sign_key) {
                $sign_info = $modelJdSign->getInfo([
                    'user_id' => $user_id,
                    'jd_user_id' => $jd_user_info['id'],
                    'sign_key' => $sign_key,
                ]);
                // 不存在该签到条目
                if (!$sign_info) {
                    $insert_res = $modelJdSign->insert([
                        'user_id' => $user_id,
                        'jd_user_id' => $jd_user_info['id'],
                        'sign_key' => $sign_key,
                        'status' => 1,
                        'add_time' => NOW_TIME,
                        'edit_time' => NOW_TIME,
                    ]);
                    if ($insert_res === false) {
                        throw new \Library\Exception\Exception(\PhalApi\T('系统异常'));
                    }
                } else {
                    if ($sign_info['status'] != 1) {
                        $update_res = $modelJdSign->update($sign_info['id'], ['status' => 1, 'edit_time' => NOW_TIME]);
                        if ($update_res === false) {
                            throw new \Library\Exception\Exception(\PhalApi\T('系统异常'));
                        }
                    }
                }
            }
            $update_all_where['NOT sign_key'] = $data;
        }
        $update_res = $modelJdSign->updateByWhere($update_all_where, $update_all_data);
        if ($update_res === false) {
            throw new \Library\Exception\Exception(\PhalApi\T('系统异常'));
        }
    }

    /**
     * 测试 钩子
     * @throws \Library\Exception\Exception
     */
    public static function test()
    {
        return self::doBeanSign(1);
    }

    /**
     * 执行签到领京豆
     * @param int $jd_user_id
     * @throws \Library\Exception\Exception
     */
    public static function doBeanSign(int $jd_user_id)
    {
        $modelJdSign = self::getModel('jdSign');
        $jd_sign_info = $modelJdSign->getInfo([
            'id' => intval($jd_user_id),
            'sign_key' => 'bean',
            'status' => 1,
        ]);
        if (!$jd_sign_info) {
            throw new \Library\Exception\Exception(\PhalApi\T("不存在该签到或用户未开启该签到|jd_user_id|{$jd_user_id}|sign_key|bean"));
        }
        $modelJdUser = self::getModel('jdUser');
        $jd_user_info = $modelJdUser->getInfo([
            'id' => intval($jd_user_id),
        ]);
        // 设置请求所需的cookie
        self::$user_cookie = [
            'pt_key' => $jd_user_info['pt_key'],
            'pt_pin' => $jd_user_info['pt_pin'],
            'pt_token' => $jd_user_info['pt_token'],
        ];
        $sign_info = self::beanSignInfo();
        switch ($sign_info['status']) {
            case 1:
                throw new \Library\Exception\Exception(\PhalApi\T('已签到'));
                break;
            case 2:
                // 进行签到
                $return_data = self::beanSign();
                $modelJdUser->updateByWhere([
                    'jd_user_id' => intval($jd_user_id),
                    'sign_key' => 'bean',
                ], [
                    'last_time' => time(),
                    'return_data' => $return_data,
                ]);
                break;
            case 3:
                throw new \Library\Exception\Exception(\PhalApi\T('请更新登录状态cookie'));
                break;
            default:
                self::DI()->logger->info('未知的京东登录状态码', $sign_info);
                break;
        }
    }

    /**
     * 京豆 签到状态
     * @param bool $status
     * @return array|mixed
     */
    public static function beanSignStatusName($status = false)
    {
        $names = [
            1 => '已签到',
            2 => '未签到',
            3 => '未登录',
        ];
        if ($names !== false) {
            return $names[$status];
        }
        return $names;
    }

    /**
     * 请求操作
     * @param $url
     * @return mixed
     * @throws \Library\Exception\Exception
     */
    public static function requestData($url)
    {
        self::DI()->curl->setCookie(self::$user_cookie);
        $res = self::DI()->curl->json_get($url);
        $code = $res['code'] ?? false;
        if ($code === false) {
            throw new \Library\Exception\Exception(\PhalApi\T('接口异常'));
        }
        $data = $res['data'] ?? [];
        $errorCode = $res['errorCode'] ?? false;
        $errorMessage = $res['errorMessage'] ?? false;
        if (!empty($errorMessage)) {
            self::DI()->logger->error("请求返回错误|URL|{$url}", $res);
            throw new \Library\Exception\Exception($res['errorMessage']);
        }
        return $data;
    }

    /**
     * 京东APP 京豆 签到信息
     * @return array
     * @throws \Library\Exception\Exception
     */
    public static function beanSignInfo()
    {
        $base_url = 'https://api.m.jd.com/client.action';
        $query_params = [
            'functionId' => 'findBeanIndex',
            'body' => json_encode([
                'source' => '',
                'monitor_refer' => '',
                'rnVersion' => '3.9',
                'rnClient' => '1',
                'monitor_source' => 'bean_m_bean_index',
            ]),
            'appid' => 'ld',
            'client' => 'android',
            'clientVersion' => ' nexus 5 build/mra58n) applewebkit/537.36 (khtml, like gecko) chrome/79.0.3945.117 mobile safari/537.36',
            'networkType' => '',
            'osVersion' => '',
            'uuid' => '',
            // 'jsonp' => 'jsonp_1578980968527_84080',
        ];

        $url = $base_url . '?' . http_build_query($query_params);

        $data = self::requestData($url);
        // 签到状态 根据测试, 1 表示已签到, 2 表示未签到, 3 表示未登录
        $sign_status = $data['status'] ?? 0;
        // if ($sign_status == 1) {
        //     throw new \Library\Exception\Exception(\PhalApi\T('已签到'));
        // } else if ($sign_status == 3) {
        //     throw new \Library\Exception\Exception(\PhalApi\T('请更新登录状态cookie'));
        // }
        // 签到天数
        $sign_days = $data['continuousDays'] ?? 0;
        // 我持有的京豆数量
        $beans_count = $data['totalUserBean'] ?? 0;
        return [
            'status' => $sign_status,
            'days' => $sign_days,
            'count' => $beans_count,
        ];
    }

    /**
     * 京东APP 京豆 签到
     * @throws \Library\Exception\Exception
     */
    public static function beanSign()
    {
        $base_url = 'https://api.m.jd.com/client.action';
        $query_params = [
            'functionId' => 'signBeanIndex',
            'body' => json_encode([
                // 'source' => '',
                'monitor_refer' => '',
                'rnVersion' => '3.9',
                'fp' => '-1',
                'shshshfp' => '-1',
                'shshshfpa' => '-1',
                'referUrl' => '-1',
                'userAgent' => '-1',
                'jda' => '-1',
                // 'rnClient' => '1',
                'monitor_source' => 'bean_m_bean_index',
            ]),
            'appid' => 'ld',
            'client' => 'android',
            'clientVersion' => ' nexus 5 build/mra58n) applewebkit/537.36 (khtml, like gecko) chrome/79.0.3945.117 mobile safari/537.36',
            'networkType' => '',
            'osVersion' => '',
            'uuid' => '',
            // 'jsonp' => 'jsonp_1578980968527_84080',
        ];

        $url = $base_url . '?' . http_build_query($query_params);

        $data = self::requestData($url);
        return $data;
        /*{
            "signedRan": "B",
            "status": "2",
            "beanUserType": 1,
            "awardType": "1",
            "dailyAward": {
                "type": "1",
                "title": "今天已签到，",
                "subTitle": "获得奖励",
                "beanAward": {
                    "beanCount": "3"
                }
            }
        }*/

        // $sign_status = $data['status'];
        // $sign_dailyAward = $data['dailyAward'];
        // $sign_awardType = $data['awardType'];
        //
        // self::DI()->logger->debug('京豆签到', $data);
        // die;

    }

    /**
     * 种豆得豆 信息
     */
    public static function plantBeanInfo()
    {
        $base_url = 'https://api.m.jd.com/client.action';
        $query_params = [
            'functionId' => 'plantBeanIndex',
            'body' => json_encode([
                'monitor_refer' => '',
                'wxHeadImgUrl' => '',
                'shareUuid' => '',
                'followType' => '1',
                'monitor_source' => 'plant_m_plant_index',
                'version' => '8.4.0.0',
            ]),
            'appid' => 'ld',
            'client' => 'android',
            'clientVersion' => ' nexus 5 build/mra58n) applewebkit/537.36 (khtml, like gecko) chrome/79.0.3945.117 mobile safari/537.36',
            'networkType' => '',
            'osVersion' => '',
            'uuid' => '',
            // 'jsonp' => 'jsonp_1578980968527_84080',
        ];

        $url = $base_url . '?' . http_build_query($query_params);

        $data = self::requestData($url);

        /*{
            "entryId": "2tpyf45pvqnbyoqbns6eertieu",
            "roundList": [
                {
                    "roundId": "vowiazorildtmoqbns6eertieu",
                    "roundState": "1",
                    "headImgUrl": "https://m.360buyimg.com/njmobilecms/jfs/t1/90318/19/8959/35705/5e08d1a1Eafe96b32/0f06adcc4c617431.png",
                    "awardState": "6",
                    "awardBeans": "23",
                    "beanState": "3",
                    "growth": "55",
                    "redDot": "2",
                    "tipBeanEndTitle": "本轮已开奖"
                },
                {
                    "roundId": "n3kp6xjxvxogcoqbns6eertieu",
                    "roundState": "2",
                    "headImgUrl": "https://m.360buyimg.com/njmobilecms/jfs/t1/110630/15/2959/35624/5e0b74c4Ed04cc99f/865a0f6b7a21fbb6.png",
                    "awardState": "1",
                    "beanState": "2",
                    "growth": "12",
                    "nutrients": "3",
                    "redDot": "2"
                },
                {
                    "roundId": "cmkhsqh32ln22oqbns6eertieu",
                    "roundState": "3",
                    "headImgUrl": "https://m.360buyimg.com/njmobilecms/jfs/t1/93656/5/9179/35440/5e0b769cE36df9a0c/5771b3d8f3ad7f50.png",
                    "awardState": "1",
                    "growth": "0",
                    "nutrients": "0",
                    "redDot": "2"
                }
            ],
            "accessFlag": "2",
            "roundAccessFag": "2",
            "timeNutrientsRes": {
                "state": "1",
                "bottleState": "3",
                "nutrCount": "1"
            }
        }*/

        $entryId = $data['entryId'];
        // 每轮信息数组
        $roundList = $data['roundList'];
        // 本轮信息
        $this_round_info = $roundList[1] ?? [];
        // 本轮ID
        $this_round_id = $this_round_info['roundId'];
        // 本轮现持有营养液数量
        $this_round_nutrients = $this_round_info['nutrients'] ?? 0;
        // 可获取营养液信息
        $timeNutrientsRes = $data['timeNutrientsRes'];
        // 可领取状态 1：可领取 2：7点再来领取（promptText）3：等待生成
        $timeNutrientsResState = $timeNutrientsRes['state'];
        // 下次可领取时间 时间戳
        $nextReceiveTime = substr($timeNutrientsRes['nextReceiveTime'], 0, 10);
        // 本次可领取数量
        $nutrCount = $timeNutrientsRes['nutrCount'];

        var_dump(date('Y-m-d H:i:s', $nextReceiveTime));
        die;


        self::DI()->logger->debug('种豆得豆 信息', $data);
        die;
    }

    /**
     * 收取营养液
     */
    public static function receiveNutrients()
    {
        $base_url = 'https://api.m.jd.com/client.action';
        $query_params = [
            'functionId' => 'receiveNutrients',
            'body' => json_encode([
                'roundId' => 'n3kp6xjxvxogcoqbns6eertieu',
                'monitor_source' => 'plant_m_plant_index',
                'monitor_refer' => 'plant_receiveNutrients',
                'version' => '8.4.0.0',
            ]),
            'appid' => 'ld',
            'client' => 'android',
            'clientVersion' => ' nexus 5 build/mra58n) applewebkit/537.36 (khtml, like gecko) chrome/79.0.3945.117 mobile safari/537.36',
            'networkType' => '',
            'osVersion' => '',
            'uuid' => '',
            // 'jsonp' => 'jsonp_1578980968527_84080',
        ];

        $url = $base_url . '?' . http_build_query($query_params);

        $data = self::requestData($url);
        // 本次收取数量
        $nutrients = $data['nutrients'];
        // 下次收取时间 时间戳
        $nextReceiveTime = substr($data['nextReceiveTime'], 0, 10);

        self::DI()->logger->debug('收取营养液', $data);
        die;
    }

    /**
     * 培养京豆
     */
    public static function cultureBean()
    {
        $base_url = 'https://api.m.jd.com/client.action';
        $query_params = [
            'functionId' => 'cultureBean',
            'body' => json_encode([
                'roundId' => 'n3kp6xjxvxogcoqbns6eertieu',
                'monitor_source' => 'plant_m_plant_index',
                'monitor_refer' => 'plant_index',
                'version' => '8.4.0.0',
            ]),
            'appid' => 'ld',
            'client' => 'android',
            'clientVersion' => ' nexus 5 build/mra58n) applewebkit/537.36 (khtml, like gecko) chrome/79.0.3945.117 mobile safari/537.36',
            'networkType' => '',
            'osVersion' => '',
            'uuid' => '',
            // 'jsonp' => 'jsonp_1578980968527_84080',
        ];

        $url = $base_url . '?' . http_build_query($query_params);

        $data = self::requestData($url);

        self::DI()->logger->debug('培养京豆', $data);
        die;
    }

    /**
     * 获取京东用户信息
     * @param array $data
     * @return array
     * @throws \Library\Exception\Exception
     */
    public static function getJDUserInfo(array $data)
    {
        $url = 'https://wq.jd.com/user_new/info/GetJDUserInfoUnion?orgFlag=JD_PinGou_New&callSource=mainorder&channel=4&isHomewhite=0&sceneval=2&_=1579075497576&sceneval=2&g_login_type=1&callback=GetJDUserInfoUnion&g_ty=ls';
        self::DI()->curl->setCookie([
            'pt_key' => $data['pt_key'],
            'pt_pin' => $data['pt_pin'],
            'pt_token' => $data['pt_token'],
        ]);
        self::DI()->curl->setHeader([
            'referer' => 'https://home.m.jd.com/myJd/newhome.action',
        ]);
        $res = self::DI()->curl->get($url);
        $jsonp = self::DI()->tool->trimSpaceInStr($res);
        $json = str_replace('try{GetJDUserInfoUnion(', '', $jsonp);
        $json = str_replace(');}catch(e){}', '', $json);
        $result = json_decode($json, true);
        if ($result['retcode'] != 0 || empty($result['data'])) {
            self::DI()->logger->error('获取京东会员信息错误', $res);
            throw new \Library\Exception\Exception(\PhalApi\T('未知错误'));
        }
        $data = $result['data'] ?? [];
        $user_name = $data['userInfo']['baseInfo']['curPin'];
        $nick_name = $data['userInfo']['baseInfo']['nickname'];
        $level_name = $data['userInfo']['baseInfo']['levelName'];
        return [
            'user_name' => $user_name,
            'nick_name' => $nick_name,
            'level_name' => $level_name,
        ];

    }

}
