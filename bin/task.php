<?php
defined('API_ROOT') || define('API_ROOT', dirname(dirname(__FILE__)));
require_once API_ROOT . '/public/init.php';// 引入核心文件

//只允许CLI命令调用，不能通过网址调用
$param_arr = getopt('a:n:u:t:');
$argv = [
    $param_arr['a'] ?? false,// action
    $param_arr['t'] ?? false,// type
    $param_arr['n'] ?? false,// 备用
    $param_arr['u'] ?? false,// 备用
];

$argv[0] || die('Access Invalid!');

set_time_limit(0);
ignore_user_abort(true);
try {
    switch ($argv[0]) {
        case 'tieba':
            switch ($argv[1]) {
                case 'sign':// 签到
                    $di->logger->info('执行定时:贴吧签到');
                    \Common\Domain\TieBa::doSignAll();// 签到所有贴吧
                    break;
                case 'retry':// 重试
                    $di->logger->info('执行定时:贴吧重试签到');
                    \Common\Domain\TieBa::doRetryAll();// 重试所有出错贴吧
                    break;
                case 'send_info':
                    $di->logger->info('推送签到详情信息');
                    $wechat_domain = new \Common\Domain\WeChatPublicPlatform();
                    $wechat_domain->sendTieBaSignDetailByCron();
                    break;
                default:
                    die('非法参数');
                    break;
            }
            break;
        case 'jd':
            switch ($argv[1]) {
                case 'send_info':
                    $di->logger->info('推送签到详情信息');
                    $wechat_domain = new \Common\Domain\WeChatPublicPlatform();
                    $wechat_domain->sendJDSignDetailByCron();
                    break;
                case 'test':
                    \Common\Domain\JdSign::test();
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
                default:
                    die('非法参数');
                    break;
            }
            $domain_JdSign=new \Common\Domain\JdSign();
            $domain_JdSign->setSignKey($argv[1])->doItemSignAll();// 执行签到项目
            break;
        default:
            break;
    }

} catch (Exception $e) {
    $di->logger->error($e->getMessage());
    echo $e->getMessage();
}

