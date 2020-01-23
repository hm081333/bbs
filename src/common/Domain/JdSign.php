<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2018-11-26
 * Time: 11:14:57
 */

namespace Common\Domain;


use Library\DateHelper;

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
    static $roundId = '';
    static $paradiseUuid = '';

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
     * @throws \Library\Exception\BadRequestException
     */
    public static function test()
    {
        return self::doVVipClubAll();
    }

    /**
     * 执行签到领京豆 - 所有
     * @throws \Library\Exception\BadRequestException
     * @throws \Library\Exception\Exception
     */
    public static function doBeanSignAll()
    {
        $modelJdSign = self::getModel('JdSign');
        $offset = 0;
        $limit = 100;
        while (true) {
            $jd_sign_list = $modelJdSign->getListLimitByWhere($limit, $offset, [
                // 类型为签到
                'ly_jd_sign.sign_key' => 'bean',
                // 任务状态为正常
                'ly_jd_sign.status' => 1,
                // 登录状态为正常
                'jd_user.status' => 1,
            ], 'ly_jd_sign.id asc', 'ly_jd_sign.id,ly_jd_sign.return_data,jd_user.pt_key,jd_user.pt_pin,jd_user.pt_token');
            if (empty($jd_sign_list)) {
                break;
            }
            foreach ($jd_sign_list as $jd_sign_info) {
                try {
                    self::doBeanSign(0, $jd_sign_info);
                } catch (\Exception $e) {
                    self::DI()->logger->info("执行签到领京豆|异常|{$e->getMessage()}", $jd_sign_info);
                }
            }
            $offset += $limit;
        }
    }

    /**
     * 执行签到领京豆
     * @param int   $jd_sign_id
     * @param array $jd_sign_info
     * @throws \Library\Exception\BadRequestException
     * @throws \Library\Exception\Exception
     */
    public static function doBeanSign(int $jd_sign_id, array $jd_sign_info = [])
    {
        $sign_key = 'bean';
        $modelJdSign = self::getModel('JdSign');
        if (empty($jd_sign_info)) {
            if (empty($jd_sign_id)) {
                throw new \Library\Exception\BadRequestException(\PhalApi\T('错误请求'));
            }
            $jd_sign_info = $modelJdSign->getInfo([
                'ly_jd_sign.id' => intval($jd_sign_id),
                // 类型为签到
                'ly_jd_sign.sign_key' => $sign_key,
                // 任务状态为正常
                'ly_jd_sign.status' => 1,
                // 登录状态为正常
                'jd_user.status' => 1,
            ], 'ly_jd_sign.id,ly_jd_sign.return_data,jd_user.pt_key,jd_user.pt_pin,jd_user.pt_token');
        }
        if (empty($jd_sign_info)) {
            throw new \Library\Exception\Exception(\PhalApi\T("不存在该签到或用户未开启该签到|jd_sign_id|{$jd_sign_id}|sign_key|bean"));
        }
        $jd_sign_id = intval($jd_sign_info['id']);

        $jd_sign_info['return_data'] = unserialize($jd_sign_info['return_data']);
        $day_begin = strtotime(date('Y-m-d'));
        if (($jd_sign_info['return_data']['signTime'] ?? 0) >= $day_begin) {
            return;
            // throw new \Library\Exception\Exception(\PhalApi\T('今天已签到'));
        }

        // 设置请求所需的cookie
        self::$user_cookie = [
            'pt_key' => $jd_sign_info['pt_key'],
            'pt_pin' => $jd_sign_info['pt_pin'],
            'pt_token' => $jd_sign_info['pt_token'],
        ];
        $sign_info = self::beanSignInfo();
        switch ($sign_info['status']) {
            case 1:
                throw new \Library\Exception\Exception(\PhalApi\T('已签到'));
                break;
            case 2:
                // 进行签到
                $return_data = self::beanSign();
                $modelJdSign->updateByWhere([
                    'id' => $jd_sign_id,
                    'sign_key' => $sign_key,
                    'status' => 1,
                ], [
                    'last_time' => time(),
                    'return_data' => serialize([
                        'status' => $return_data['status'],
                        'dailyAward' => $return_data['dailyAward'],
                        'signTime' => time(),
                    ]),
                ]);
                break;
            case 3:
                throw new \Library\Exception\Exception(\PhalApi\T('请更新登录状态cookie'));
                break;
            default:
                self::DI()->logger->info('未知的京东签到状态码', $sign_info);
                break;
        }
    }

    /**
     * 执行种豆得豆 - 所有
     * @throws \Library\Exception\BadRequestException
     */
    public static function doPlantBeanAll()
    {
        $modelJdSign = self::getModel('JdSign');
        $offset = 0;
        $limit = 100;
        while (true) {
            $jd_sign_list = $modelJdSign->getListLimitByWhere($limit, $offset, [
                // 类型为种豆得豆
                'ly_jd_sign.sign_key' => 'plant',
                // 任务状态为正常
                'ly_jd_sign.status' => 1,
                // 登录状态为正常
                'jd_user.status' => 1,
            ], 'ly_jd_sign.id asc', 'ly_jd_sign.id,ly_jd_sign.return_data,jd_user.pt_key,jd_user.pt_pin,jd_user.pt_token');
            if (empty($jd_sign_list)) {
                break;
            }
            foreach ($jd_sign_list as $jd_sign_info) {
                try {
                    self::doPlantBean(0, $jd_sign_info);
                } catch (\Exception $e) {
                    self::DI()->logger->info("执行种豆得豆|异常|{$e->getMessage()}", $jd_sign_info);
                }
            }
            $offset += $limit;
        }
    }

    /**
     * 执行种豆得豆
     * @param int   $jd_sign_id
     * @param array $jd_sign_info
     * @throws \Library\Exception\BadRequestException
     * @throws \Library\Exception\Exception
     */
    public static function doPlantBean(int $jd_sign_id, array $jd_sign_info = [])
    {
        $sign_key = 'plant';
        $modelJdSign = self::getModel('JdSign');
        if (empty($jd_sign_info)) {
            if (empty($jd_sign_id)) {
                throw new \Library\Exception\BadRequestException(\PhalApi\T('错误请求'));
            }
            $jd_sign_info = $modelJdSign->getInfo([
                'ly_jd_sign.id' => intval($jd_sign_id),
                // 类型为签到
                'ly_jd_sign.sign_key' => $sign_key,
                // 任务状态为正常
                'ly_jd_sign.status' => 1,
                // 登录状态为正常
                'jd_user.status' => 1,
            ], 'ly_jd_sign.id,ly_jd_sign.return_data,jd_user.pt_key,jd_user.pt_pin,jd_user.pt_token');
        }
        if (empty($jd_sign_info)) {
            throw new \Library\Exception\Exception(\PhalApi\T("不存在该签到或用户未开启该签到|jd_sign_id|{$jd_sign_id}|sign_key|bean"));
        }
        $jd_sign_id = intval($jd_sign_info['id']);

        // $jd_sign_info['return_data'] = unserialize($jd_sign_info['return_data']);
        // $next_time = $jd_sign_info['return_data']['next_time'] ?? 0;
        // if ($next_time > time()) {
        //     return;
        //     // throw new \Library\Exception\Exception(\PhalApi\T('未到下次收取时间'));
        // }

        // 设置请求所需的cookie
        self::$user_cookie = [
            'pt_key' => $jd_sign_info['pt_key'],
            'pt_pin' => $jd_sign_info['pt_pin'],
            'pt_token' => $jd_sign_info['pt_token'],
        ];
        // 现持有营养液数量 - 初始值
        $nutrients = 0;
        $return_data = [];
        // 种豆得豆相关信息
        $plant_info = self::plantBeanInfo();
        // 持有数量
        $nutrients = $plant_info['nutrients'];
        // 下次收取时间
        $return_data['next_time'] = $plant_info['nextReceiveTime'];
        // 可收取营养液数量大于0
        if ($plant_info['nutrCount'] > 0) {
            $receive_info = self::receiveNutrients();
            // 持有数量
            $nutrients = $receive_info['nutrients'];
            // 下次收取时间
            $receive_info['next_time'] = $plant_info['nextReceiveTime'];
        }
        // 帮好友收取
        $plant_friend_reward = self::receivePlantFriend();
        // 收取得到的奖励累积到本次该收取的数量
        $nutrients += $plant_friend_reward;
        // 持有营养液
        if ($nutrients > 0) {
            // 使用营养液 培养京豆
            self::cultureBean();
        }

        // 状态2：7点再来领取
        if ($plant_info['timeNutrientsResState'] == 2) {
            // 明天早上7点的时间戳
            $return_data['next_time'] = strtotime(date('Y-m-d 7:00:00') . '+ 1 day');
        }

        $modelJdSign->updateByWhere([
            'id' => $jd_sign_id,
            'sign_key' => $sign_key,
            'status' => 1,
        ], [
            'last_time' => time(),
            'return_data' => serialize($return_data),
        ]);

    }

    /**
     * 执行京享值领京豆 - 所有
     * @throws \Library\Exception\BadRequestException
     */
    public static function doVVipClubAll()
    {
        $modelJdSign = self::getModel('JdSign');
        $offset = 0;
        $limit = 100;
        while (true) {
            $jd_sign_list = $modelJdSign->getListLimitByWhere($limit, $offset, [
                // 类型为京享值领京豆
                'ly_jd_sign.sign_key' => 'vvipclub',
                // 任务状态为正常
                'ly_jd_sign.status' => 1,
                // 登录状态为正常
                'jd_user.status' => 1,
            ], 'ly_jd_sign.id asc', 'ly_jd_sign.id,ly_jd_sign.return_data,jd_user.pt_key,jd_user.pt_pin,jd_user.pt_token');
            if (empty($jd_sign_list)) {
                break;
            }
            foreach ($jd_sign_list as $jd_sign_info) {
                try {
                    self::doVVipClub(0, $jd_sign_info);
                } catch (\Exception $e) {
                    self::DI()->logger->info("执行京享值领京豆|异常|{$e->getMessage()}", $jd_sign_info);
                }
            }
            $offset += $limit;
        }
    }

    /**
     * 执行京享值领京豆
     * @param int   $jd_sign_id
     * @param array $jd_sign_info
     * @throws \Library\Exception\BadRequestException
     * @throws \Library\Exception\Exception
     */
    public static function doVVipClub(int $jd_sign_id, array $jd_sign_info = [])
    {
        $sign_key = 'vvipclub';
        $modelJdSign = self::getModel('JdSign');
        if (empty($jd_sign_info)) {
            if (empty($jd_sign_id)) {
                throw new \Library\Exception\BadRequestException(\PhalApi\T('错误请求'));
            }
            $jd_sign_info = $modelJdSign->getInfo([
                'ly_jd_sign.id' => intval($jd_sign_id),
                // 类型为签到
                'ly_jd_sign.sign_key' => $sign_key,
                // 任务状态为正常
                'ly_jd_sign.status' => 1,
                // 登录状态为正常
                'jd_user.status' => 1,
            ], 'ly_jd_sign.id,ly_jd_sign.return_data,jd_user.pt_key,jd_user.pt_pin,jd_user.pt_token');
        }
        if (empty($jd_sign_info)) {
            throw new \Library\Exception\Exception(\PhalApi\T("不存在该签到或用户未开启该签到|jd_sign_id|{$jd_sign_id}|sign_key|bean"));
        }
        $jd_sign_id = intval($jd_sign_info['id']);

        $jd_sign_info['return_data'] = unserialize($jd_sign_info['return_data']);
        $day_begin = strtotime(date('Y-m-d'));
        if (($jd_sign_info['return_data']['signTime'] ?? 0) >= $day_begin) {
            return;
            // throw new \Library\Exception\Exception(\PhalApi\T('今天已签到'));
        }

        // 设置请求所需的cookie
        self::$user_cookie = [
            'pt_key' => $jd_sign_info['pt_key'],
            'pt_pin' => $jd_sign_info['pt_pin'],
            'pt_token' => $jd_sign_info['pt_token'],
        ];

        // 完成所有任务，获取免费次数
        self::vvipclub_doTaskAll();

        $return_data = [];
        // 京享值领京豆相关信息
        $luckyBox_info = self::vvipclub_luckyBox();
        if ($luckyBox_info['freeTimes'] <= 0) {
            return;
        }

        for ($i = 0; $i < $luckyBox_info['freeTimes']; $i++) {
            self::vvipclub_shaking();
        }

        // 明天早上7点的时间戳
        $return_data['signTime'] = time();

        $modelJdSign->updateByWhere([
            'id' => $jd_sign_id,
            'sign_key' => $sign_key,
            'status' => 1,
        ], [
            'last_time' => time(),
            'return_data' => serialize($return_data),
        ]);

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
        if (isset($res['success'])) {
            $data = [];
            if ($res['success']) {
                $data = $res['data'];
            } else {
                self::DI()->logger->error('请求失败', $res);
                throw new \Library\Exception\Exception($res['message']);
            }
        } else {
            $code = $res['code'] ?? false;
            if ($code === false) {
                self::DI()->logger->error('接口异常', $res);
                throw new \Library\Exception\Exception(\PhalApi\T('接口异常'));
            }
            $data = $res['data'] ?? [];
            $errorCode = $res['errorCode'] ?? false;
            $errorMessage = $res['errorMessage'] ?? false;
            if (!empty($errorMessage)) {
                self::DI()->logger->error("请求返回错误|URL|{$url}", $res);
                throw new \Library\Exception\Exception($res['errorMessage']);
            }
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
     * @return array
     * @throws \Library\Exception\Exception
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

        // $entryId = $data['entryId'];
        // 每轮信息数组
        $roundList = $data['roundList'];
        // 本轮信息
        $this_round_info = $roundList[1] ?? [];
        // 本轮ID
        $this_round_id = $this_round_info['roundId'];
        self::$roundId = $this_round_id;
        // 本轮现持有营养液数量
        $this_round_nutrients = $this_round_info['nutrients'] ?? 0;
        // 可获取营养液信息
        $timeNutrientsRes = $data['timeNutrientsRes'];
        // 可领取状态 1：可领取 2：7点再来领取（promptText）3：等待生成
        $timeNutrientsResState = $timeNutrientsRes['state'];
        // 下次可领取时间 时间戳
        $nextReceiveTime = substr($timeNutrientsRes['nextReceiveTime'] ?? '0', 0, 10);
        // 本次可领取数量
        $nutrCount = $timeNutrientsRes['nutrCount'] ?? 0;

        return [
            // 'entryId' => $entryId,
            'roundId' => $this_round_id,
            'nutrients' => $this_round_nutrients,
            // 'timeNutrientsRes' => $timeNutrientsRes,
            'timeNutrientsResState' => $timeNutrientsResState,
            'nextReceiveTime' => $nextReceiveTime,
            'nutrCount' => $nutrCount,
        ];

        // var_dump(date('Y-m-d H:i:s', $nextReceiveTime));
        // die;
        //
        //
        // self::DI()->logger->debug('种豆得豆 信息', $data);
        // die;
    }

    /**
     * 收取营养液
     * @return array
     * @throws \Library\Exception\Exception
     */
    public static function receiveNutrients()
    {
        $base_url = 'https://api.m.jd.com/client.action';
        $query_params = [
            'functionId' => 'receiveNutrients',
            'body' => json_encode([
                'roundId' => self::$roundId,
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
        $nextReceiveTime = substr($data['nextReceiveTime'] ?? '0', 0, 10);

        self::DI()->logger->debug('收取营养液', $data);

        return [
            'nutrients' => $nutrients,
            'nextReceiveTime' => $nextReceiveTime,
        ];
    }

    /**
     * 种豆得豆好友收集营养液
     * @return array
     * @throws \Library\Exception\Exception
     */
    public static function receivePlantFriend()
    {
        // 可收集好友列表页码
        $pageNum = 1;
        // 获取奖励的总数量
        $nutrients = 0;
        while (true) {
            // 可收集好友列表
            $list = self::plantFriendList($pageNum);
            if (empty($list)) {
                break;
            }
            foreach ($list as $item) {
                // 可收集数量
                $nutrCount = $item['nutrCount'] ?? 0;
                // 数量少的不收取，因为数量少的肯定不会有奖励
                if ($nutrCount >= 3) {
                    self::$paradiseUuid = $item['paradiseUuid'];
                    $collect_res = self::collectUserNutr();
                    // 累积总奖励
                    $nutrients += $collect_res['collectNutrRewards'];
                    // 间隔 1s 防止出现 抱歉，活动太火爆了 提示
                    sleep(2);
                }
            }
            // 页码+1 - 下一页
            $pageNum += 1;
        }
        return $nutrients;
        // return [
        //     'nutrients' => $nutrients,
        // ];
    }

    /**
     * 种豆得豆好友列表
     * @param int $pageNum
     * @return array|mixed
     * @throws \Library\Exception\Exception
     */
    public static function plantFriendList($pageNum = 1)
    {
        $base_url = 'https://api.m.jd.com/client.action';
        $query_params = [
            'functionId' => 'plantFriendList',
            'body' => json_encode([
                'pageNum' => (string)$pageNum,
                'monitor_source' => 'plant_m_plant_index',
                'monitor_refer' => 'plantFriendList',
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
        if (isset($data['tips'])) {
            return [];
        }

        // self::DI()->logger->debug('种豆得豆好友列表', $data);

        return $data['friendInfoList'] ?? [];
    }

    /**
     * 收取用户的营养液
     * @return array|mixed
     * @throws \Library\Exception\Exception
     */
    public static function collectUserNutr()
    {
        $base_url = 'https://api.m.jd.com/client.action';
        $query_params = [
            'functionId' => 'collectUserNutr',
            'body' => json_encode([
                'paradiseUuid' => self::$paradiseUuid,
                'roundId' => self::$roundId,
                'monitor_source' => 'plant_m_plant_index',
                'monitor_refer' => 'collectUserNutr',
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

        self::DI()->logger->debug('收取用户的营养液', $data);

        // 收取结果 1 收取成功 2 没有可收取的营养液 3
        $collectResult = $data['collectResult'];
        // 帮忙收取获得的奖励 营养液 数量
        $collectNutrRewards = $data['collectNutrRewards'] ?? 0;

        return [
            'collectResult' => $collectResult,
            'collectNutrRewards' => $collectNutrRewards,
        ];
    }

    /**
     * 培养京豆
     * @throws \Library\Exception\Exception
     */
    public static function cultureBean()
    {
        $base_url = 'https://api.m.jd.com/client.action';
        $query_params = [
            'functionId' => 'cultureBean',
            'body' => json_encode([
                'roundId' => self::$roundId,
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

        return $data;
    }

    /**
     * 京享值领京豆 信息
     * @return mixed
     * @throws \Library\Exception\Exception
     */
    public static function vvipclub_luckyBox()
    {
        $base_url = 'https://api.m.jd.com/client.action';
        $query_params = [
            'functionId' => 'vvipclub_luckyBox',
            'body' => json_encode([
                'info' => 'freeTimes,title,beanNum,useBeanNum,imgUrl',
            ]),
            'appid' => 'vip_h5',
        ];

        $url = $base_url . '?' . http_build_query($query_params);
        $data = self::requestData($url);

        self::DI()->logger->debug('京享值领京豆', $data);

        return $data;
    }

    /**
     * 京享值领京豆 摇一摇
     * @return mixed
     * @throws \Library\Exception\Exception
     */
    public static function vvipclub_shaking()
    {
        $base_url = 'https://api.m.jd.com/client.action';
        $query_params = [
            'functionId' => 'vvipclub_shaking',
            'body' => json_encode([
                'type' => '0',
                'riskInfo' => [
                    'platform' => 3,
                    'pageClickKey' => 'MJDVip_Shake',
                    // 'eid' => 'AT44TEP6BH5QHGV7AE5NED2EY6TBYYTNOXO7FYXGWRNVVPCZBM7KSACH3WX6CCH6NJYSBNDFKZELYB2UTWRFXK5NHQ',
                    // 'fp' => '7f8a82fdd6584f4afcba1f69f1eebe42',
                    // 'shshshfp' => '3a6f70a53124ab6c1c14dac8c8f6553e',
                    // 'shshshfpa' => 'b57728e0-9c76-3ba0-cad4-b6a185c849a4-1567159746',
                    // 'shshshfpb' => 'tVBqHpN7OyXYgPxIVBqY9vg==',
                ],
            ]),
            'appid' => 'vip_h5',
        ];

        $url = $base_url . '?' . http_build_query($query_params);
        $data = self::requestData($url);

        // self::DI()->logger->debug('京享值领京豆 摇一摇', $data);

        return $data;
    }

    /**
     * 京享值领京豆 完成所有任务
     * @throws \Library\Exception\Exception
     */
    public static function vvipclub_doTaskAll()
    {
        $list = self::vvipclub_lotteryTaskList();
        foreach ($list as $item) {
            $taskName = $item['taskName'];
            $item = self::vvipclub_lotteryTaskInfo($taskName);
            // 不存在该任务 信息
            if (empty($item)) {
                continue;
            }
            $item = $item[0] ?? [];
            // 该任务已完成
            if ($item['currentFinishTimes'] >= $item['totalPrizeTimes']) {
                continue;
            }
            // 任务列表
            $taskItems = $item['taskItems'];
            for ($i = $item['currentFinishTimes']; $i < $item['totalPrizeTimes']; $i++) {
                $taskItemId = $taskItems[$i]['id'];
                self::vvipclub_doTask($taskName, $taskItemId);
            }
        }
    }

    /**
     * 京享值领京豆 完成任务
     * @param $taskName
     * @param $taskItemId
     * @return bool|mixed
     */
    public static function vvipclub_doTask($taskName, $taskItemId)
    {
        $base_url = 'https://api.m.jd.com/client.action';
        $query_params = [
            'appid' => 'vip_h5',
            'functionId' => 'vvipclub_doTask',
            'body' => json_encode([
                'taskName' => $taskName,
                'taskItemId' => $taskItemId,
            ]),
        ];

        $url = $base_url . '?' . http_build_query($query_params);
        try {
            $data = self::requestData($url);
            // self::DI()->logger->debug('京享值领京豆 完成任务', $data);
            return $data;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 京享值领京豆 任务列表
     * @return mixed
     * @throws \Library\Exception\Exception
     */
    public static function vvipclub_lotteryTaskList()
    {
        $base_url = 'https://api.m.jd.com/client.action';
        $query_params = [
            'appid' => 'vip_h5',
            'functionId' => 'vvipclub_lotteryTask',
            // 'body' => json_encode([
            //     'info' => 'shareTask,browseTask,attentionTask',
            //     'withItem' => false,
            // ]),
        ];

        $url = $base_url . '?' . http_build_query($query_params);
        $data = self::requestData($url);

        // self::DI()->logger->debug('京享值领京豆 任务列表', $data);

        return $data;
    }

    /**
     * 京享值领京豆 任务详情
     * @param $taskName
     * @return bool|mixed
     */
    public static function vvipclub_lotteryTaskInfo($taskName)
    {
        $base_url = 'https://api.m.jd.com/client.action';
        $query_params = [
            'appid' => 'vip_h5',
            'functionId' => 'vvipclub_lotteryTask',
            'body' => json_encode([
                'info' => $taskName,
                'withItem' => true,
            ]),
        ];

        $url = $base_url . '?' . http_build_query($query_params);
        try {
            $data = self::requestData($url);
            // self::DI()->logger->debug('京享值领京豆 任务详情', $data);
            return $data;
        } catch (\Exception $e) {
            return false;
        }
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
