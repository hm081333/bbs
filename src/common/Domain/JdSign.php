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
    static $lotteryCode = '';

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
        $modelJdUser = self::getModel('JdUser');
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
     */
    public static function test()
    {
    }

    /**
     * 执行签到领京豆 - 所有
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
            throw new \Library\Exception\Exception(\PhalApi\T("不存在该签到或用户未开启该签到|jd_sign_id|{$jd_sign_id}|sign_key|{$sign_key}"));
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
                $award = [];
                if ($return_data['awardType'] == 1) {
                    // 普通签到奖励
                    $award = $return_data['dailyAward'];
                } else if ($return_data['awardType'] == 2) {
                    // 连续签到奖励
                    $award = $return_data['continuityAward'];
                } else {
                    self::DI()->logger->error('京东APP签到|未知签到奖励类型', $return_data);
                }
                $modelJdSign->updateByWhere([
                    'id' => $jd_sign_id,
                    'sign_key' => $sign_key,
                    'status' => 1,
                ], [
                    'last_time' => time(),
                    'return_data' => serialize([
                        'status' => $return_data['status'],
                        'dailyAward' => $award,
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
            throw new \Library\Exception\Exception(\PhalApi\T("不存在该签到或用户未开启该签到|jd_sign_id|{$jd_sign_id}|sign_key|{$sign_key}"));
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
        self::$roundId = $plant_info['roundId'];
        // 种豆得豆任务
        $awardList = $plant_info['awardList'];
        foreach ($awardList as $award) {
            // limitFlag为2代表任务已完成
            // if ($award['limitFlag'] == 2) continue;
            if (isset($award['childAwardList'])) {
                foreach ($award['childAwardList'] as $childAward) {
                    self::doPlantBeanAward($childAward);
                }
                unset($childAward);
            } else {
                self::doPlantBeanAward($award);
            }
        }
        // 持有数量
        $nutrients = $plant_info['nutrients'];
        // 下次收取时间
        $return_data['next_time'] = $plant_info['nextReceiveTime'];
        // 可收取营养液数量大于0
        if ($plant_info['nutrCount'] > 0) {
            $receive_info = self::receiveNutrients($plant_info['roundId']);
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
            self::cultureBean($plant_info['roundId']);
        }

        // awardState： 1：培养中 5：待领取 6：已领取
        // beanState： 2：发芽 4：成豆
        $last_round_info = $plant_info['last_round'];
        // 上轮京豆未收取
        if (isset($last_round_info['roundId']) && $last_round_info['awardState'] == 5) {
            // 收取京豆
            self::receivedBean($last_round_info['roundId']);
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
            throw new \Library\Exception\Exception(\PhalApi\T("不存在该签到或用户未开启该签到|jd_sign_id|{$jd_sign_id}|sign_key|{$sign_key}"));
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
     * 执行 福利转盘 - 所有
     */
    public static function doWheelSurfAll()
    {
        $modelJdSign = self::getModel('JdSign');
        $offset = 0;
        $limit = 100;
        while (true) {
            $jd_sign_list = $modelJdSign->getListLimitByWhere($limit, $offset, [
                // 类型为京享值领京豆
                'ly_jd_sign.sign_key' => 'wheelSurf',
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
                    self::doWheelSurf(0, $jd_sign_info);
                } catch (\Exception $e) {
                    self::DI()->logger->info("福利转盘|异常|{$e->getMessage()}", $jd_sign_info);
                }
            }
            $offset += $limit;
        }
    }

    /**
     * 执行 福利转盘
     * @param int   $jd_sign_id
     * @param array $jd_sign_info
     * @throws \Library\Exception\BadRequestException
     * @throws \Library\Exception\Exception
     */
    public static function doWheelSurf(int $jd_sign_id, array $jd_sign_info = [])
    {
        $sign_key = 'wheelSurf';
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
            throw new \Library\Exception\Exception(\PhalApi\T("不存在该签到或用户未开启该签到|jd_sign_id|{$jd_sign_id}|sign_key|{$sign_key}"));
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

        $return_data = [];
        // 京享值领京豆相关信息
        $info = self::wheelSurfIndex();

        if ($info['lotteryCount'] <= 0) {
            return;
        }

        for ($i = 0; $i < $info['lotteryCount']; $i++) {
            self::lotteryDraw();
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
     * 执行 京东金融APP签到 - 所有
     */
    public static function doJRSignAll()
    {
        $modelJdSign = self::getModel('JdSign');
        $offset = 0;
        $limit = 100;
        while (true) {
            $jd_sign_list = $modelJdSign->getListLimitByWhere($limit, $offset, [
                // 类型为京享值领京豆
                'ly_jd_sign.sign_key' => 'jrSign',
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
                    self::doJRSign(0, $jd_sign_info);
                } catch (\Exception $e) {
                    self::DI()->logger->info("京东金融APP签到|异常|{$e->getMessage()}", $jd_sign_info);
                }
            }
            $offset += $limit;
        }
    }

    /**
     * 执行 京东金融APP签到
     * @param int   $jd_sign_id
     * @param array $jd_sign_info
     * @throws \Library\Exception\BadRequestException
     * @throws \Library\Exception\Exception
     */
    public static function doJRSign(int $jd_sign_id, array $jd_sign_info = [])
    {
        $sign_key = 'jrSign';
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
            throw new \Library\Exception\Exception(\PhalApi\T("不存在该签到或用户未开启该签到|jd_sign_id|{$jd_sign_id}|sign_key|{$sign_key}"));
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

        $return_data = [];
        // 京享值领京豆相关信息
        $isSign = self::JRSignInfo();

        if ($isSign) {
            return;
        }

        self::JRSign();

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
     * 执行 领取双签礼包 - 所有
     */
    public static function doDoubleSignAll()
    {
        $modelJdSign = self::getModel('JdSign');
        $offset = 0;
        $limit = 100;
        while (true) {
            $jd_sign_list = $modelJdSign->getListLimitByWhere($limit, $offset, [
                // 类型为京享值领京豆
                'ly_jd_sign.sign_key' => 'doubleSign',
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
                    self::doDoubleSign(0, $jd_sign_info);
                } catch (\Exception $e) {
                    self::DI()->logger->info("领取双签礼包|异常|{$e->getMessage()}", $jd_sign_info);
                }
            }
            $offset += $limit;
        }
    }

    /**
     * 执行 领取双签礼包
     * @param int   $jd_sign_id
     * @param array $jd_sign_info
     * @throws \Library\Exception\BadRequestException
     * @throws \Library\Exception\Exception
     */
    public static function doDoubleSign(int $jd_sign_id, array $jd_sign_info = [])
    {
        $sign_key = 'doubleSign';
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
            throw new \Library\Exception\Exception(\PhalApi\T("不存在该签到或用户未开启该签到|jd_sign_id|{$jd_sign_id}|sign_key|{$sign_key}"));
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

        $return_data = [];

        self::doubleSign();
        // 领取双签送的营养液
        self::receiveNutrientsTask('7');

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
     * 执行 京东金融APP - 提升白条额度 - 所有
     */
    public static function doJRRiseLimitAll()
    {
        $modelJdSign = self::getModel('JdSign');
        $offset = 0;
        $limit = 100;
        while (true) {
            $jd_sign_list = $modelJdSign->getListLimitByWhere($limit, $offset, [
                // 类型为京享值领京豆
                'ly_jd_sign.sign_key' => 'jrRiseLimit',
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
                    self::doJRRiseLimit(0, $jd_sign_info);
                } catch (\Exception $e) {
                    self::DI()->logger->info("提升白条额度|异常|{$e->getMessage()}", $jd_sign_info);
                }
            }
            $offset += $limit;
        }
    }

    /**
     * 执行 京东金融APP - 提升白条额度
     * @param int   $jd_sign_id
     * @param array $jd_sign_info
     * @throws \Library\Exception\BadRequestException
     * @throws \Library\Exception\Exception
     */
    public static function doJRRiseLimit(int $jd_sign_id, array $jd_sign_info = [])
    {
        $sign_key = 'jrRiseLimit';
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
            throw new \Library\Exception\Exception(\PhalApi\T("不存在该签到或用户未开启该签到|jd_sign_id|{$jd_sign_id}|sign_key|{$sign_key}"));
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

        $return_data = [];

        $info = self::JRRiseLimitInfo();
        self::JRRiseLimit($info);

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
     * 执行 京东金融APP - 翻牌赢钢镚 - 所有
     */
    public static function doJRFlopRewardAll()
    {
        $modelJdSign = self::getModel('JdSign');
        $offset = 0;
        $limit = 100;
        while (true) {
            $jd_sign_list = $modelJdSign->getListLimitByWhere($limit, $offset, [
                // 类型为京享值领京豆
                'ly_jd_sign.sign_key' => 'jrFlopReward',
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
                    self::doJRFlopReward(0, $jd_sign_info);
                } catch (\Exception $e) {
                    self::DI()->logger->info("翻牌赢钢镚|异常|{$e->getMessage()}", $jd_sign_info);
                }
            }
            $offset += $limit;
        }
    }

    /**
     * 执行 京东金融APP - 翻牌赢钢镚
     * @param int   $jd_sign_id
     * @param array $jd_sign_info
     * @throws \Library\Exception\BadRequestException
     * @throws \Library\Exception\Exception
     */
    public static function doJRFlopReward(int $jd_sign_id, array $jd_sign_info = [])
    {
        $sign_key = 'jrFlopReward';
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
            throw new \Library\Exception\Exception(\PhalApi\T("不存在该签到或用户未开启该签到|jd_sign_id|{$jd_sign_id}|sign_key|{$sign_key}"));
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

        $return_data = [];

        // 查询是否能翻牌
        $can_flop = self::JRFlopRewardInfo();
        if (!$can_flop) {
            return;
        }

        self::JRFlopReward();

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
     * 执行 京东金融APP - 金币抽奖 - 所有
     */
    public static function doJRLotteryAll()
    {
        $modelJdSign = self::getModel('JdSign');
        $offset = 0;
        $limit = 100;
        while (true) {
            $jd_sign_list = $modelJdSign->getListLimitByWhere($limit, $offset, [
                // 类型为京享值领京豆
                'ly_jd_sign.sign_key' => 'jrLottery',
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
                    self::doJRLottery(0, $jd_sign_info);
                } catch (\Exception $e) {
                    self::DI()->logger->info("金币抽奖|异常|{$e->getMessage()}", $jd_sign_info);
                }
            }
            $offset += $limit;
        }
    }

    /**
     * 执行 京东金融APP - 金币抽奖
     * @param int   $jd_sign_id
     * @param array $jd_sign_info
     * @throws \Library\Exception\BadRequestException
     * @throws \Library\Exception\Exception
     */
    public static function doJRLottery(int $jd_sign_id, array $jd_sign_info = [])
    {
        $sign_key = 'jrLottery';
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
            throw new \Library\Exception\Exception(\PhalApi\T("不存在该签到或用户未开启该签到|jd_sign_id|{$jd_sign_id}|sign_key|{$sign_key}"));
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

        $return_data = [];

        // 查询是否能翻牌
        $info = self::JRLotteryInfo();
        if ($info === false || $info > 0) {
            return;
        }

        self::JRLottery();

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
     * 执行 京东金融APP - 每日赚京豆签到 - 所有
     */
    public static function doJRSignRecordsAll()
    {
        $modelJdSign = self::getModel('JdSign');
        $offset = 0;
        $limit = 100;
        while (true) {
            $jd_sign_list = $modelJdSign->getListLimitByWhere($limit, $offset, [
                // 类型为京享值领京豆
                'ly_jd_sign.sign_key' => 'jrSignRecords',
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
                    self::doJRSignRecords(0, $jd_sign_info);
                } catch (\Exception $e) {
                    self::DI()->logger->info("每日赚京豆签到|异常|{$e->getMessage()}", $jd_sign_info);
                }
            }
            $offset += $limit;
        }
    }

    /**
     * 执行 京东金融APP - 每日赚京豆签到
     * @param int   $jd_sign_id
     * @param array $jd_sign_info
     * @throws \Library\Exception\BadRequestException
     * @throws \Library\Exception\Exception
     */
    public static function doJRSignRecords(int $jd_sign_id, array $jd_sign_info = [])
    {
        $sign_key = 'jrSignRecords';
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
            throw new \Library\Exception\Exception(\PhalApi\T("不存在该签到或用户未开启该签到|jd_sign_id|{$jd_sign_id}|sign_key|{$sign_key}"));
        }
        $jd_sign_id = intval($jd_sign_info['id']);

        $jd_sign_info['return_data'] = unserialize($jd_sign_info['return_data']);
        $day_begin = strtotime(date('Y-m-d'));
        if (($jd_sign_info['return_data']['signTime'] ?? 0) >= $day_begin) {
            // return;
            // throw new \Library\Exception\Exception(\PhalApi\T('今天已签到'));
        }

        // 设置请求所需的cookie
        self::$user_cookie = [
            'pt_key' => $jd_sign_info['pt_key'],
            'pt_pin' => $jd_sign_info['pt_pin'],
            'pt_token' => $jd_sign_info['pt_token'],
        ];

        $return_data = [];

        self::JRSignRecords();

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
     * @param      $url
     * @param bool $data
     * @return array|bool
     * @throws \Library\Exception\Exception
     */
    public static function jdRequest($url, $data = false)
    {
        self::DI()->curl->setCookie(self::$user_cookie);
        $res = $data === false ? self::DI()->curl->json_get($url) : self::DI()->curl->json_post($url, $data);
        // self::DI()->logger->debug('', $res);
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
            } else if ($code == 0) {
                // 正常
            } else if ($code == 3) {
                /** @var $domainJdUser \Common\Domain\JdUser */
                $domainJdUser = self::getDomain('JdUser');
                $domainJdUser::loginStatusExpired(self::$user_cookie);
                throw new \Library\Exception\Exception(\PhalApi\T('请更新登录状态cookie'));
            } else {
                self::DI()->logger->error('京东返回未知状态|jdRequest', $res);
            }
            $data = $res['data'] ?? $res;
            $errorCode = $res['errorCode'] ?? false;
            $errorMessage = $res['errorMessage'] ?? false;
            if (!empty($errorMessage)) {
                self::DI()->logger->error("请求返回错误|URL|{$url}", $res);
                throw new \Library\Exception\Exception($res['errorMessage']);
            }
        }
        sleep(2);
        return $data;
    }

    /**
     * 京东APP 京豆 签到信息
     * @return array
     * @throws \Library\Exception\Exception
     */
    public static function beanSignInfo()
    {
        $url = self::buildURL('https://api.m.jd.com/client.action', [
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
        ]);

        $data = self::jdRequest($url);
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
        $url = self::buildURL('https://api.m.jd.com/client.action', [
            'functionId' => 'signBeanIndex',
            'body' => json_encode([
                // 'source' => '',
                'monitor_refer' => '',
                'rnVersion' => '3.9',
                'monitor_source' => 'bean_m_bean_index',
            ]),
            'appid' => 'ld',
            'client' => 'android',
            'clientVersion' => ' nexus 5 build/mra58n) applewebkit/537.36 (khtml, like gecko) chrome/79.0.3945.117 mobile safari/537.36',
            'networkType' => '',
            'osVersion' => '',
            'uuid' => '',
        ]);

        $data = self::jdRequest($url);
        self::DI()->logger->debug('京东APP签到', $data);
        return $data;
    }

    /**
     * 种豆得豆 信息
     * @return array
     * @throws \Library\Exception\Exception
     */
    public static function plantBeanInfo()
    {
        $url = self::buildURL('https://api.m.jd.com/client.action', [
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
        ]);

        $data = self::jdRequest($url);
        // self::DI()->logger->debug('种豆得豆 信息', $data);

        // $entryId = $data['entryId'];
        // 每轮信息数组
        $roundList = $data['roundList'];
        // self::DI()->logger->debug('roundList', $roundList);
        // 上轮信息
        $last_round_info = $roundList[0] ?? [];
        // 本轮信息
        $this_round_info = $roundList[1] ?? [];
        // 本轮ID
        $this_round_id = $this_round_info['roundId'];
        // self::$roundId = $this_round_id;
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
        // 任务列表
        $awardList = $data['awardList'];

        return [
            // 'entryId' => $entryId,
            'last_round' => $last_round_info,
            'roundId' => $this_round_id,
            'nutrients' => $this_round_nutrients,
            // 'timeNutrientsRes' => $timeNutrientsRes,
            'timeNutrientsResState' => $timeNutrientsResState,
            'nextReceiveTime' => $nextReceiveTime,
            'nutrCount' => $nutrCount,
            'awardList' => $awardList,
        ];
    }

    /**
     * 收取营养液
     * @param bool $roundId
     * @return array|bool
     * @throws \Library\Exception\Exception
     */
    public static function receiveNutrients($roundId = false)
    {
        if (!$roundId) {
            return false;
        }
        $url = self::buildURL('https://api.m.jd.com/client.action', [
            'functionId' => 'receiveNutrients',
            'body' => json_encode([
                'roundId' => $roundId,
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
        ]);

        $data = self::jdRequest($url);
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
     * 完成种豆得豆任务
     * @param bool $info
     * @return bool
     * @throws \Library\Exception\Exception
     */
    public static function doPlantBeanAward($info = false)
    {
        // limitFlag为2代表任务已完成
        if ($info === false || $info['limitFlag'] == 2) return false;

        // var_dump($info['awardName'] . ' ---- ' . $info['awardType']);
        // 每日签到 ---- 1
        // 浏览店铺 ---- 3
        // 关注商品 ---- 5
        // 邀请好友 ---- 2
        // 医药会场 ---- 4
        // 金融双签 ---- 7
        switch (intval($info['awardType'])) {
            case 3:
                self::shopTaskList();
                break;
            case 4:
                self::purchaseRewardTask();
                break;
            case 5:
                self::productTaskList();
                break;
            default:
                break;
        }
        return true;
    }

    /**
     * 种豆得豆任务
     * @param bool $awardType 7签到
     * @return array|bool|void
     * @throws \Library\Exception\Exception
     */
    public static function receiveNutrientsTask($awardType = false)
    {
        if ($awardType === false) {
            return;
        }
        $url = self::buildURL('https://api.m.jd.com/client.action', [
            'functionId' => 'receiveNutrientsTask',
        ]);

        $data = self::jdRequest($url, [
            'body' => json_encode([
                'monitor_refer' => 'plant_receiveNutrientsTask',
                'monitor_source' => 'plant_app_plant_index',
                'awardType' => (string)$awardType,
                'version' => '8.4.0.0',
            ]),
            'appid' => 'ld',
            'client' => 'android',
            'clientVersion' => ' nexus 5 build/mra58n) applewebkit/537.36 (khtml, like gecko) chrome/79.0.3945.117 mobile safari/537.36',
            'networkType' => '',
            'osVersion' => '',
            'uuid' => '',
        ]);

        self::DI()->logger->debug("完成奖励营养液的任务|awardType|{$awardType}", $data);

        return $data;
    }

    /**
     * 种豆得豆任务 - 逛逛会场
     * @return array|bool
     * @throws \Library\Exception\Exception
     */
    public static function purchaseRewardTask()
    {
        $url = self::buildURL('https://api.m.jd.com/client.action', [
            'functionId' => 'purchaseRewardTask',
        ]);

        $data = self::jdRequest($url, [
            'body' => json_encode([
                'monitor_refer' => 'plant_purchaseRewardTask',
                'monitor_source' => 'plant_app_plant_index',
                'roundId' => self::$roundId,
                'version' => '8.4.0.0',
            ]),
            'appid' => 'ld',
            'client' => 'android',
            'clientVersion' => ' nexus 5 build/mra58n) applewebkit/537.36 (khtml, like gecko) chrome/79.0.3945.117 mobile safari/537.36',
            'networkType' => '',
            'osVersion' => '',
            'uuid' => '',
        ]);

        self::DI()->logger->debug("完成奖励营养液的任务 - 逛逛会场", $data);

        return $data;
    }

    /**
     * 种豆得豆任务 - 关注店铺 - 列表
     * @return array|bool
     * @throws \Library\Exception\Exception
     */
    public static function shopTaskList()
    {
        $url = self::buildURL('https://api.m.jd.com/client.action', [
            'functionId' => 'shopTaskList',
        ]);

        $data = self::jdRequest($url, [
            'body' => json_encode([
                'monitor_refer' => 'plant_shopList',
                'monitor_source' => 'plant_app_plant_index',
                'version' => '8.4.0.0',
            ]),
            'appid' => 'ld',
            'client' => 'android',
            'clientVersion' => ' nexus 5 build/mra58n) applewebkit/537.36 (khtml, like gecko) chrome/79.0.3945.117 mobile safari/537.36',
            'networkType' => '',
            'osVersion' => '',
            'uuid' => '',
        ]);

        // self::DI()->logger->debug("种豆得豆任务 - 关注店铺 - 列表", $data);

        $goodShopList = $data['goodShopList'] ?? [];
        $moreShopList = $data['moreShopList'] ?? [];
        foreach ($goodShopList as $item) {
            // 任务状态为2表示还能领取营养液
            if ($item['taskState'] == 2) {
                self::shopNutrientsTask($item['shopId'], $item['shopTaskId']);
            }
        }
        unset($goodShopList, $item);
        foreach ($moreShopList as $item) {
            // 任务状态为2表示还能领取营养液
            if ($item['taskState'] == 2) {
                self::shopNutrientsTask($item['shopId'], $item['shopTaskId']);
            }
        }
        unset($moreShopList, $item);

        return true;
    }

    /**
     * 种豆得豆任务 - 关注店铺
     * @param $shopId
     * @param $shopTaskId
     * @return array|bool
     * @throws \Library\Exception\Exception
     */
    public static function shopNutrientsTask($shopId = false, $shopTaskId = false)
    {
        if (!$shopId || !$shopTaskId) {
            return false;
        }
        $url = self::buildURL('https://api.m.jd.com/client.action', [
            'functionId' => 'shopNutrientsTask',
        ]);

        $data = self::jdRequest($url, [
            'body' => json_encode([
                'monitor_refer' => 'plant_shopNutrientsTask',
                'monitor_source' => 'plant_app_plant_index',
                'shopId' => (string)$shopId,
                'shopTaskId' => (string)$shopTaskId,
                'version' => '8.4.0.0',
            ]),
            'appid' => 'ld',
            'client' => 'android',
            'clientVersion' => ' nexus 5 build/mra58n) applewebkit/537.36 (khtml, like gecko) chrome/79.0.3945.117 mobile safari/537.36',
            'networkType' => '',
            'osVersion' => '',
            'uuid' => '',
        ]);
        self::DI()->logger->debug("种豆得豆任务 - 关注店铺", $data);
        // 取消关注店铺
        self::JDFollowShop($shopId);

        return $data;
    }

    /**
     * 关注店铺、取消关注店铺
     * @param bool $shopId
     * @param bool $follow
     * @return array|bool
     * @throws \Library\Exception\Exception
     */
    public static function JDFollowShop($shopId = false, $follow = false)
    {
        if (!$shopId) {
            return false;
        }
        $follow = !$follow ? 'DelShopFav' : 'AddShopFav';
        $url = self::buildURL("https://wq.jd.com/fav/shop/{$follow}", [
            'shopId' => (string)$shopId,
            'venderId' => (string)$shopId,
            'sceneval' => '2',
            'g_login_type' => '1',
            // 'callback' => 'jsonpCBKO',
            'g_ty' => 'ls',
        ]);
        self::DI()->curl->setCookie(self::$user_cookie);
        self::DI()->curl->setHeader([
            'referer' => "https://shop.m.jd.com/?shopId={$shopId}",
        ]);
        $data = self::DI()->curl->json_get($url);
        self::DI()->curl->unsetHeader('referer');
        if ($data['iRet'] != 0) {
            throw new \Library\Exception\Exception($data['errMsg']);
        }

        // self::DI()->logger->debug('关注店铺、取消关注店铺', $data);

        return true;
    }

    /**
     * 种豆得豆任务 - 关注商品 - 列表
     * @return array|bool
     * @throws \Library\Exception\Exception
     */
    public static function productTaskList()
    {
        $url = self::buildURL('https://api.m.jd.com/client.action', [
            'functionId' => 'productTaskList',
        ]);

        $data = self::jdRequest($url, [
            'body' => json_encode([
                'monitor_refer' => 'plant_productTaskList',
                'monitor_source' => 'plant_app_plant_index',
                'version' => '8.4.0.0',
            ]),
            'appid' => 'ld',
            'client' => 'android',
            'clientVersion' => ' nexus 5 build/mra58n) applewebkit/537.36 (khtml, like gecko) chrome/79.0.3945.117 mobile safari/537.36',
            'networkType' => '',
            'osVersion' => '',
            'uuid' => '',
        ]);

        // self::DI()->logger->debug("种豆得豆任务 - 关注商品 - 列表", $data);

        $productInfoList = $data['productInfoList'] ?? [];
        foreach ($productInfoList as $item) {
            foreach ($item as $info) {
                // 任务状态为2表示还能领取营养液
                if ($info['taskState'] == 2) {
                    self::productNutrientsTask($info['skuId'], $info['productTaskId']);
                }
            }
        }
        unset($productInfoList, $item);

        return true;
    }

    /**
     * 种豆得豆任务 - 关注商品
     * @param $skuId
     * @param $productTaskId
     * @return array|bool
     * @throws \Library\Exception\Exception
     */
    public static function productNutrientsTask($skuId = false, $productTaskId = false)
    {
        if (!$skuId || !$productTaskId) {
            return false;
        }
        $url = self::buildURL('https://api.m.jd.com/client.action', [
            'functionId' => 'productNutrientsTask',
        ]);

        $data = self::jdRequest($url, [
            'body' => json_encode([
                'monitor_refer' => 'plant_productNutrientsTask',
                'monitor_source' => 'plant_app_plant_index',
                'productTaskId' => (string)$productTaskId,
                'skuId' => (string)$skuId,
                'version' => '8.4.0.0',
            ]),
            'appid' => 'ld',
            'client' => 'android',
            'clientVersion' => ' nexus 5 build/mra58n) applewebkit/537.36 (khtml, like gecko) chrome/79.0.3945.117 mobile safari/537.36',
            'networkType' => '',
            'osVersion' => '',
            'uuid' => '',
        ]);
        self::DI()->logger->debug("种豆得豆任务 - 关注商品", $data);
        // 取消收藏商品
        self::JDFavoriteGood($skuId);

        return $data;
    }

    /**
     * 收藏商品、取消收藏商品
     * @param bool $skuId
     * @param bool $favorite
     * @return array|bool
     * @throws \Library\Exception\Exception
     */
    public static function JDFavoriteGood($skuId = false, $favorite = false)
    {
        if (!$skuId) {
            return false;
        }
        $favorite = !$favorite ? 'FavCommDel' : 'FavCommAdd';
        $url = self::buildURL("https://wq.jd.com/fav/comm/{$favorite}", [
            // 'shopId' => (string)'',
            'commId' => (string)$skuId,
            'sceneval' => '2',
        ]);
        self::DI()->curl->setCookie(self::$user_cookie);
        self::DI()->curl->setHeader([
            'referer' => "https://shop.m.jd.com/?shopId={$skuId}",
        ]);
        $data = self::DI()->curl->json_get($url);
        self::DI()->curl->unsetHeader('referer');
        if ($data['iRet'] != 0) {
            throw new \Library\Exception\Exception($data['errMsg']);
        }

        // self::DI()->logger->debug('关注店铺、取消关注店铺', $data);

        return true;
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
                    // sleep(2);
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
        $url = self::buildURL('https://api.m.jd.com/client.action', [
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
        ]);

        $data = self::jdRequest($url);
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
        $url = self::buildURL('https://api.m.jd.com/client.action', [
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
        ]);

        $data = self::jdRequest($url);

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
     * @param bool|string $roundId
     * @return array|bool
     * @throws \Library\Exception\Exception
     */
    public static function cultureBean($roundId = false)
    {
        if (!$roundId) {
            return false;
        }
        $url = self::buildURL('https://api.m.jd.com/client.action', [
            'functionId' => 'cultureBean',
            'body' => json_encode([
                'roundId' => $roundId,
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
        ]);

        $data = self::jdRequest($url);

        self::DI()->logger->debug('培养京豆', $data);

        return $data;
    }

    /**
     * 收取京豆
     * @param $roundId
     * @return array|bool
     * @throws \Library\Exception\Exception
     */
    public static function receivedBean($roundId = false)
    {
        if (!$roundId) {
            return false;
        }
        $url = self::buildURL('https://api.m.jd.com/client.action', [
            'functionId' => 'receivedBean',
            'body' => json_encode([
                'monitor_refer' => 'plant_index',
                'monitor_source' => 'plant_m_plant_index',
                'roundId' => $roundId,
                'version' => '8.4.0.0',
            ]),
            'appid' => 'ld',
            'client' => 'android',
            'clientVersion' => ' nexus 5 build/mra58n) applewebkit/537.36 (khtml, like gecko) chrome/79.0.3945.117 mobile safari/537.36',
            'networkType' => '',
            'osVersion' => '',
            'uuid' => '',
        ]);

        $data = self::jdRequest($url);

        self::DI()->logger->debug('收取京豆', $data);

        return $data;
    }

    /**
     * 京享值领京豆 信息
     * @return mixed
     * @throws \Library\Exception\Exception
     */
    public static function vvipclub_luckyBox()
    {
        $url = self::buildURL('https://api.m.jd.com/client.action', [
            'functionId' => 'vvipclub_luckyBox',
            'body' => json_encode([
                'info' => 'freeTimes,title,beanNum,useBeanNum,imgUrl',
            ]),
            'appid' => 'vip_h5',
        ]);

        $data = self::jdRequest($url);

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
        $url = self::buildURL('https://api.m.jd.com/client.action', [
            'functionId' => 'vvipclub_shaking',
            'body' => json_encode([
                'type' => '0',
                'riskInfo' => [
                    'platform' => 3,
                    'pageClickKey' => 'MJDVip_Shake',
                ],
            ]),
            'appid' => 'vip_h5',
        ]);

        $data = self::jdRequest($url);

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
        $url = self::buildURL('https://api.m.jd.com/client.action', [
            'appid' => 'vip_h5',
            'functionId' => 'vvipclub_doTask',
            'body' => json_encode([
                'taskName' => $taskName,
                'taskItemId' => $taskItemId,
            ]),
        ]);

        try {
            $data = self::jdRequest($url);
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
        $url = self::buildURL('https://api.m.jd.com/client.action', [
            'appid' => 'vip_h5',
            'functionId' => 'vvipclub_lotteryTask',
            // 'body' => json_encode([
            //     'info' => 'shareTask,browseTask,attentionTask',
            //     'withItem' => false,
            // ]),
        ]);

        $data = self::jdRequest($url);

        // self::DI()->logger->debug('京享值领京豆 任务列表', $data);

        return $data;
    }

    /**
     * 京享值领京豆 任务详情
     * @param $taskName
     * @return array|bool
     * @throws \Library\Exception\Exception
     */
    public static function vvipclub_lotteryTaskInfo($taskName)
    {
        $url = self::buildURL('https://api.m.jd.com/client.action', [
            'appid' => 'vip_h5',
            'functionId' => 'vvipclub_lotteryTask',
            'body' => json_encode([
                'info' => $taskName,
                'withItem' => true,
            ]),
        ]);

        try {
            $data = self::jdRequest($url);
            // self::DI()->logger->debug('京享值领京豆 任务详情', $data);
            return $data;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 福利转盘 详情
     * @return mixed
     * @throws \Library\Exception\Exception
     */
    public static function wheelSurfIndex()
    {
        $url = self::buildURL('https://api.m.jd.com/client.action', [
            'functionId' => 'wheelSurfIndex',
            'body' => json_encode([
                'actId' => 'jgpqtzjhvaoym',
                'appSource' => 'jdhome',
            ]),
            'appid' => 'ld',
            'client' => 'android',
            'clientVersion' => ' nexus 5 build/mra58n) applewebkit/537.36 (khtml, like gecko) chrome/79.0.3945.117 mobile safari/537.36',
            'networkType' => '',
            'osVersion' => '',
            'uuid' => '',
        ]);

        $data = self::jdRequest($url);
        self::$lotteryCode = $data['lotteryCode'] ?? '';
        // self::DI()->logger->debug('福利转盘 详情', $data);
        return $data;
    }

    /**
     * 福利转盘 抽奖
     * @return mixed
     * @throws \Library\Exception\Exception
     */
    public static function lotteryDraw()
    {
        $url = self::buildURL('https://api.m.jd.com/client.action', [
            'functionId' => 'lotteryDraw',
            'body' => json_encode([
                'actId' => 'jgpqtzjhvaoym',
                'appSource' => 'jdhome',
                'lotteryCode' => self::$lotteryCode,
            ]),
            'appid' => 'ld',
            'client' => 'android',
            'clientVersion' => ' nexus 5 build/mra58n) applewebkit/537.36 (khtml, like gecko) chrome/79.0.3945.117 mobile safari/537.36',
            'networkType' => '',
            'osVersion' => '',
            'uuid' => '',
        ]);

        try {
            $data = self::jdRequest($url);
            // self::DI()->logger->debug('福利转盘 抽奖', $data);
            return $data;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 京东金融 APP 请求操作
     * @param      $url
     * @param bool $data
     * @return bool
     * @throws \Library\Exception\Exception
     */
    public static function jrRequest($url, $data = false)
    {
        self::DI()->curl->setCookie(self::$user_cookie);

        $res = empty($data) ? self::DI()->curl->json_get($url) : self::DI()->curl->json_post($url, $data);
        $resultCode = $res['resultCode'] ?? 0;

        if ($resultCode == 3) {
            /** @var $domainJdUser \Common\Domain\JdUser */
            $domainJdUser = self::getDomain('JdUser');
            $domainJdUser::loginStatusExpired(self::$user_cookie);
            throw new \Library\Exception\Exception(\PhalApi\T('请更新登录状态cookie'));
        } else if ($resultCode == 0) {
            $data = $res['resultData'];
        } else {
            self::DI()->logger->debug('京东金融返回未知状态', $res);
            throw new \Library\Exception\Exception(\PhalApi\T('请求失败'));
        }

        // resBusiCode
        // 15 已经领取过
        // 24 当天未签到

        sleep(2);
        return $data;
    }

    /**
     * 京东金融APP签到 信息
     * @return bool|mixed
     * @throws \Library\Exception\Exception
     */
    public static function JRSignInfo()
    {
        $url = self::buildURL('https://ms.jr.jd.com/gw/generic/gry/h5/m/querySignHistory');
        $form_data = [
            'reqData' => json_encode([
                'channelSource' => 'JRAPP',
                'riskDeviceParam' => json_encode([
                ]),
            ]),
        ];
        $res = self::jrRequest($url, $form_data);
        // self::DI()->logger->debug('京东金融APP签到 信息', $res);
        if ($res['resBusiCode'] != 0) {
            throw new \Library\Exception\Exception($res['resBusiMsg']);
        }
        // 内容
        $data = $res['resBusiData'] ?? [];
        // 今天是否已签到 - 默认值为true，防止获取失败一直重复尝试签到
        $isSign = $data['isSign'] ?? true;
        return $isSign;
    }

    /**
     * 京东金融APP签到 今天签到结果
     * @return array|mixed
     * @throws \Library\Exception\Exception
     */
    public static function JRTodaySignResult()
    {
        $url = self::buildURL('https://ms.jr.jd.com/gw/generic/gry/h5/m/queryTodaySignResult');
        $form_data = [
            'reqData' => json_encode([
                'channelSource' => 'JRAPP',
                'riskDeviceParam' => json_encode([
                ]),
            ]),
        ];

        $res = self::jrRequest($url, $form_data);
        // self::DI()->logger->debug('京东金融APP签到', $res);
        if ($res['resBusiCode'] != 0) {
            throw new \Library\Exception\Exception($res['resBusiMsg']);
        }
        $data = $res['resBusiData'] ?? [];
        return $data;
    }

    /**
     * 京东金融APP签到
     * @return array|mixed
     * @throws \Library\Exception\Exception
     */
    public static function JRSign()
    {
        $url = self::buildURL('https://ms.jr.jd.com/gw/generic/gry/h5/m/signIn');
        $form_data = [
            'reqData' => json_encode([
                'channelSource' => 'JRAPP',
                'riskDeviceParam' => json_encode([
                ]),
            ]),
        ];

        $res = self::jrRequest($url, $form_data);
        self::DI()->logger->debug('京东金融APP签到', $res);
        if ($res['resBusiCode'] != 0) {
            throw new \Library\Exception\Exception($res['resBusiMsg']);
        }
        $data = $res['resBusiData'] ?? [];
        return $data;
    }

    /**
     * 京东APP、京东金融APP 领取双签礼包
     * @return array|mixed
     * @throws \Library\Exception\Exception
     */
    public static function doubleSign()
    {
        $url = self::buildURL('https://nu.jr.jd.com/gw/generic/jrm/h5/m/process');
        $form_data = [
            'reqData' => json_encode([
                'actCode' => 'FBBFEC496C',
                // 双签相关请求类型：9 获取双签相关信息 3 领取双签礼包 12 双签领取结果
                'type' => 3,
                'frontParam' => [
                    'channel' => 'JR',
                ],
                'riskDeviceParam' => json_encode([
                ]),
            ]),
        ];

        $res = self::jrRequest($url, $form_data);
        if ($res['code'] != 200) {
            self::DI()->logger->error("领取双签礼包|{$res['msg']}", $res);
            throw new \Library\Exception\Exception($res['msg']);
        }
        $res = $res['data'];
        if ($res['businessCode'] != '000sq') {
            self::DI()->logger->error("领取双签礼包|{$res['businessMsg']}", $res);
            throw new \Library\Exception\Exception($res['businessMsg']);
        }
        $data = $res['businessData'] ?? [];
        if ($data['businessCode'] != '000sq') {
            self::DI()->logger->error("领取双签礼包|{$data['businessMsg']}", $data);
            throw new \Library\Exception\Exception($data['businessMsg']);
        }
        return $data;
    }

    /**
     * 京东金融APP 提升白条额度信息
     * @return array|mixed
     * @throws \Library\Exception\Exception
     */
    public static function JRRiseLimitInfo()
    {
        $url = self::buildURL('https://ms.jr.jd.com/gw/generic/bt/h5/m/getRiseLimitItems');

        $form_data = [
            'reqData' => json_encode([
                "riskDeviceInfo" => [
                    "appId" => "com.jd.jinrong",
                ],
            ]),
        ];

        $res = self::jrRequest($url, $form_data);
        if ($res['code'] != '0000') {
            self::DI()->logger->error("提升白条额度信息|{$res['error_msg']}", $res);
            throw new \Library\Exception\Exception($res['error_msg']);
        }
        $raiseItemList = $res['raiseItemList'] ?? [];
        $raiseItem = $raiseItemList[0] ?? [];
        // 编号 $raiseItem['uniqueCode']
        // 状态 $raiseItem['itemStatus'] 0 可领取 4 不可领取
        // 额度提升数量 $raiseItem['changeLimit']

        if (empty($raiseItem) || $raiseItem['itemStatus'] != 0) {
            return [];
        }

        return $raiseItem;
    }

    /**
     * 京东金融APP 提升白条额度
     * @param array $raiseItem
     * @return array|mixed|void
     * @throws \Library\Exception\Exception
     */
    public static function JRRiseLimit($raiseItem = [])
    {
        if (empty($raiseItem)) {
            return;
        }

        $url = self::buildURL('https://ms.jr.jd.com/gw/generic/bt/h5/m/receiveDailyQuotaPackage');
        $form_data = [
            'reqData' => json_encode([
                "packageId" => $raiseItem['uniqueCode'],
            ]),
        ];

        $res = self::jrRequest($url, $form_data);

        // self::DI()->logger->debug('提升白条额度', $res);

        if (empty($res['result'])) {
            self::DI()->logger->error("提升白条额度", $res);
            throw new \Library\Exception\Exception('返回数据结构异常');
        }
        $data = $res['result'];
        if ($data['code'] != '0' || $data['issuccess'] != 1) {
            self::DI()->logger->error("提升白条额度|{$data['error_msg']}", $data);
            throw new \Library\Exception\Exception($data['error_msg']);
        }
        return $res;
    }

    /**
     * 京东金融APP 翻牌赢钢镚 信息
     * @return int
     * @throws \Library\Exception\Exception
     */
    public static function JRFlopRewardInfo()
    {
        $url = self::buildURL('https://gps.jd.com/activity/signin/reward/home', [
            'uaType' => 2,
            'platCode' => 3,
        ]);

        self::DI()->curl->setCookie(self::$user_cookie);
        $res = self::DI()->curl->json_get($url);

        if ($res['code'] != 1) {
            self::DI()->logger->error("翻牌赢钢镚 信息|{$res['msg']}", $res);
            throw new \Library\Exception\Exception($res['msg']);
        }

        $data = $res['data'];
        if ($data['result'] != 0) {
            self::DI()->logger->error("翻牌赢钢镚 信息|返回数据异常", $data);
            throw new \Library\Exception\Exception('返回数据异常');
        }
        $isAllowSignin = 0;
        if (isset($data['isAllowSignin'])) {
            $isAllowSignin = $data['isAllowSignin'];
        } else if (isset($data['total']) && isset($data['used'])) {
            $isAllowSignin = $data['total'] > $data['used'] ? 1 : 0;
        } else {
            self::DI()->logger->error("翻牌赢钢镚 信息|返回数据发生变化", $data);
        }

        return $isAllowSignin;
    }

    /**
     * 京东金融APP 翻牌赢钢镚
     * @return mixed
     * @throws \Library\Exception\Exception
     */
    public static function JRFlopReward()
    {
        $url = self::buildURL('https://gps.jd.com/activity/signin/reward/choice', [
            'uaType' => 2,
            'position' => 1,
            'platCode' => 3,
        ]);

        self::DI()->curl->setCookie(self::$user_cookie);
        $res = self::DI()->curl->json_get($url);

        if ($res['code'] != 1) {
            self::DI()->logger->error("翻牌赢钢镚|{$res['msg']}", $res);
            throw new \Library\Exception\Exception($res['msg']);
        }

        $data = $res['data'];
        if ($data['result'] != 0) {
            self::DI()->logger->error("翻牌赢钢镚|返回数据异常", $data);
            throw new \Library\Exception\Exception('返回数据异常');
        }

        // self::DI()->logger->info("翻牌赢钢镚", $data);
        return $data;
    }

    /**
     * 京东金融APP 金币抽奖 信息
     * @return bool|mixed
     * @throws \Library\Exception\Exception
     */
    public static function JRLotteryInfo()
    {
        $url = self::buildURL('https://ms.jr.jd.com/gw/generic/hy/h5/m/lotteryInfo', [
            'reqData' => json_encode([
                'actKey' => 'AbeQry',
            ]),
        ]);

        $res = self::jrRequest($url);

        if ($res['code'] != '0000') {
            self::DI()->logger->error("金币抽奖 信息|{$res['msg']}", $res);
            throw new \Library\Exception\Exception($res['msg']);
        }
        self::DI()->logger->info("金币抽奖 信息", $res);

        $data = $res['data'];
        // 设定，消耗0金币为免费抽奖，返回数组错误时返回false
        $lotteryCoins = $data['lotteryCoins'] ?? false;

        return $lotteryCoins;
    }

    /**
     * 京东金融APP 金币抽奖
     * @return bool|mixed
     * @throws \Library\Exception\Exception
     */
    public static function JRLottery()
    {
        $url = self::buildURL('https://ms.jr.jd.com/gw/generic/hy/h5/m/lottery', [
            'reqData' => json_encode([
                'actKey' => 'AbeQry',
            ]),
        ]);

        self::DI()->curl->setHeader(['Referer' => 'https://m.jr.jd.com/member/coinlottery/index.html?channel=01-qd-190306']);
        $res = self::jrRequest($url);
        self::DI()->curl->unsetHeader('Referer');
        self::DI()->logger->info("金币抽奖", $res);

        // if ($res['code'] != '1000') {
        //     self::DI()->logger->error("金币抽奖|{$res['msg']}", $res);
        //     throw new \Library\Exception\Exception($res['msg']);
        // }
        //
        // $data = $res['data'];

        return true;
    }

    /**
     * 京东金融APP 每日赚京豆 - 签到
     * @return bool|mixed
     * @throws \Library\Exception\Exception
     */
    public static function JRSignRewardGift()
    {
        $url = self::buildURL('https://ms.jr.jd.com/gw/generic/zc/h5/m/signRewardGift');

        self::DI()->curl->setHeader(['Referer' => 'https://jddx.jd.com/m/jddnew/money/index.html?from=dlqfl']);
        $res = self::jrRequest($url, [
            'reqData' => json_encode([
                'bizLine' => 2,
                'signDate' => date('Ymd'),
                'deviceInfo' => [
                ],
                'clientType' => 'sms',
                'clientVersion' => '11.0',
            ]),
        ]);
        self::DI()->curl->unsetHeader('Referer');

        if ($res['resultCode'] != '00000') {
            self::DI()->logger->error("每日赚京豆 - 签到|{$res['resultMsg']}", $res);
            throw new \Library\Exception\Exception($res['resultMsg']);
        }

        $data = $res['data'];
        self::DI()->logger->info("每日赚京豆 - 签到", $data);

        return $res;
    }

    /**
     * 京东金融APP 每日赚京豆 - 连续签到信息
     * @return bool|mixed
     * @throws \Library\Exception\Exception
     */
    public static function JRSignRecords()
    {
        $url = self::buildURL('https://ms.jr.jd.com/gw/generic/zc/h5/m/signRecords');

        self::DI()->curl->setHeader(['Referer' => 'https://jddx.jd.com/m/jddnew/money/index.html?from=dlqfl']);
        $res = self::jrRequest($url, [
            'reqData' => json_encode([
                'bizLine' => 2,
                'deviceInfo' => [
                    // 'openUUID' => '',
                    'optType' => 'https://jddx.jd.com/m/jddnew/money/index.html?from=dlqfl',
                ],
                'clientType' => 'sms',
                'clientVersion' => '11.0',
            ]),
        ]);
        self::DI()->curl->unsetHeader('Referer');

        if ($res['resultCode'] != '00000') {
            self::DI()->logger->error("每日赚京豆 - 连续签到信息|{$res['resultMsg']}", $res);
            throw new \Library\Exception\Exception($res['resultMsg']);
        }

        $data = $res['data'];
        if (empty($data['signRecords'])) {
            self::DI()->logger->info("每日赚京豆 - 连续签到信息", $data);
            return;
        }

        foreach ($data['signRecords'] as $signRecord) {
            if ($signRecord['signDate'] == date('Ymd')) {
                if ($signRecord['signStatus'] == 2) {
                    self::JRSignRewardGift();
                }
                break;
            }
        }

        return $res;
    }

    /**
     * 构架请求
     * @param string       $url    请求地址
     * @param array|string $params 请求参数
     * @return string
     * @throws \Library\Exception\Exception
     */
    public static function buildURL($url, $params = [])
    {
        $url_info = parse_url($url);
        if (!$url_info) {
            throw new \Library\Exception\Exception('非法请求地址');
        }
        // URL携带的参数转成数组
        parse_str($url_info['query'] ?? '', $query);
        // 有传入请求参数
        if (!empty($params)) {
            if (is_string($params)) {
                // 如果开头是 ? 把 ? 去掉
                if (strpos($params, '?') === 0) {
                    $params = substr($params, 1);
                }
                // 参数转成数组
                parse_str($params, $params);
            }
        } else {
            $params = [];
        }
        // 传入参数替换原有参数
        $params = array_merge($query, $params);
        // 参数数组构建为请求字符串
        $params = http_build_query($params);

        $scheme = isset($url_info['scheme']) ? $url_info['scheme'] . ':' : '';
        $pass = isset($url_info['pass']) ? ':' . $url_info['pass'] : '';
        $user = isset($url_info['user']) ? $url_info['user'] . $pass . '@' : '';
        $host = $url_info['host'] ?? '';
        $port = isset($url_info['port']) ? ':' . $url_info['port'] : '';
        $path = $url_info['path'] ?? '';
        $query = empty($params) ? '' : '?' . $params;
        $fragment = isset($url_info['fragment']) ? '#' . $url_info['fragment'] : '';
        // scheme:[//[user[:password]@]host[:port]][/path][?query][#fragment]

        return $scheme . '//' . $user . $host . $port . $path . $query . $fragment;
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
