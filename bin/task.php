<?php
defined('API_ROOT') || define('API_ROOT', dirname(dirname(__FILE__)));
defined('START_TIME') || define('START_TIME', microtime(true));
require_once API_ROOT . '/public/init.php';// 引入核心文件

//只允许CLI命令调用，不能通过网址调用
$param_arr = getopt('a:t:');
// action
$action = empty($param_arr['a']) ? die('非法参数！') : $param_arr['a'];
// type
$type = $param_arr['t'] ?? '';

set_time_limit(0);
ignore_user_abort(true);
try {
    switch ($action) {
        case 'tieba':
            switch ($type) {
                case 'sign':// 签到
                    $di->logger->info('执行定时:贴吧签到');
                    $Domain_TieBa = new \Common\Domain\TieBa();
                    $Domain_TieBa->doSignAll();// 签到所有贴吧
                    break;
                case 'retry':// 重试
                    $di->logger->info('执行定时:贴吧重试签到');
                    $Domain_TieBa = new \Common\Domain\TieBa();
                    $Domain_TieBa->doRetryAll();// 重试所有出错贴吧
                    break;
                case 'send_info':
                    $di->logger->info('推送签到详情信息');
                    $wechat_domain = new \Common\Domain\WeChatPublicPlatform();
                    $wechat_domain->sendTieBaSignDetailByCron();
                    break;
                default:
                    die('非法参数！');
                    break;
            }
            break;
        case 'jd':
            switch ($type) {
                case 'send_info':
                    $di->logger->info('推送签到详情信息');
                    $wechat_domain = new \Common\Domain\WeChatPublicPlatform();
                    $wechat_domain->sendJDSignDetailByCron();
                    die();
                    break;
                case 'test':
                    // $domain_JdSign = new \Common\Domain\JdSign();
                    // $domain_JdSign->test();
                    new \Library\JDDailyBonus\initial();
                    die('测试');
                    break;
                case 'bean':// 签到领京豆
                    $di->logger->info('执行定时:签到领京豆');
                    break;
                case 'plant':// 种豆得豆
                    $di->logger->info('执行定时:种豆得豆');
                    break;
                case 'vvipclub':// 京享值领京豆
                    $di->logger->info('执行定时:京享值领京豆');
                    break;
                case 'wheelSurf':// 福利转盘
                    $di->logger->info('执行定时:福利转盘');
                    break;
                case 'jrSign':// 京东金融APP签到
                    $di->logger->info('执行定时:京东金融APP签到');
                    break;
                case 'doubleSign':// 领取双签礼包
                    $di->logger->info('执行定时:领取双签礼包');
                    break;
                case 'jrRiseLimit':// 提升白条额度
                    $di->logger->info('执行定时:提升白条额度');
                    break;
                case 'jrFlopReward':// 翻牌赢钢镚
                    $di->logger->info('执行定时:翻牌赢钢镚');
                    break;
                case 'jrLottery':// 金币抽奖
                    $di->logger->info('执行定时:金币抽奖');
                    break;
                case 'jrSignRecords':// 每日赚京豆签到
                    $di->logger->info('执行定时:每日赚京豆签到');
                    break;
                case 'jrLiftGoose':// 每日赚京豆签到
                    $di->logger->info('执行定时:天天提鹅');
                    break;
                default:
                    die('非法参数！');
                    break;
            }
            $domain_JdSign = new \Common\Domain\JdSign();
            $domain_JdSign->setSignKey($type)->doItemSignAll();// 执行签到项目
            break;
        case 'test':
            // 模拟生成聊天记录数据
            $model_message = new \Common\Model\ChatMessage();
            $start_time = 1593563400;
            for ($i = 0; $i < 240; $i++) {
                $add_time = $start_time + ($i * 3600);
                $model_message->insert([
                    'chat_id' => 1,
                    'user_id' => $i % 2 == 1 ? 1 : 2,
                    'message' => '现在是北京时间：' . date('Y年m月d日 H时i分s秒', $add_time),
                    'add_time' => $add_time,
                ]);
            }
            break;
        default:
            die('非法参数！');
            break;
    }
    // 精确到1纳秒(ns)
    // $di->logger->info('定时执行成功|耗时|' . sprintf('%1\$.9f', (microtime(true) - START_TIME)) . "|a|{$action}|t|{$type}");
    $di->logger->info('定时执行成功|耗时|' . sprintf('%.9f', (microtime(true) - START_TIME)) . "|a|{$action}|t|{$type}");
} catch (Exception $e) {
    $di->logger->error($e->getMessage());
    // 精确到1纳秒(ns)
    // $di->logger->info('定时执行失败|耗时|' . sprintf('%1\$.9f', (microtime(true) - START_TIME)) . "|a|{$action}|t|{$type}");
    $di->logger->info('定时执行失败|耗时|' . sprintf('%.9f', (microtime(true) - START_TIME)) . "|a|{$action}|t|{$type}");
    echo $e->getMessage();
}

