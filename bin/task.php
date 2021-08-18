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
            new \Library\JDDailyBonus\initial('mba_muid=1605981630702658105508; shshshfpa=ca5636e3-7749-a014-a89f-a41c4efba133-1605981642; shshshfpb=a0XyQtYgC75YzxbcT3BB3hg%3D%3D; 3AB9D23F7A4B3C9B=ZGN2BQVJVHUQFQAJAWHCNGY34KM46IPJCS4YYTS2EFMHZO46MPAXISPCFTXRN57XVH7PNWR3VCYXX6BKIWPEWEEGWE; whwswswws=; __jdu=1605981630702658105508; __jda=122270672.1605981630702658105508.1605981630.1606406268.1608646325.3; __jdv=122270672%7Cdirect%7C-%7Cnone%7C-%7C1608646325391; __jdc=122270672; shshshfp=dfb24b4dd09f7799ecbaa0eb3134cd92; shshshsID=715b7a945cfe3470f5686b46fbd3791b_1_1608646331651; jcap_dvzw_fp=e21f5292e03bc26b3dd28b4d721262f3$939104805299; TrackerID=Sq3KIXwORotYTQqRnAmeSCX1fRKPQOX5Py4_jREbOUAp3__GD44gRtgN7L8ymUC60VgGjNQ4ePXim-1uV-wVAaHLWhkB6VuXAjI5-eZULrFNat5qLU4UG42ZMtqSDDT_; pt_key=AAJf4f9WADBQCcbGIEiKWnda5enybLohBqvKUHZaeSsaKGkaF7ey9fW_FoqlrepB52GA5goCloA; pt_pin=hm081333; pt_token=ou2pv1uw; pwdt_id=hm081333; sfstoken=tk01maf041be7a8sMSsxKzJ4MjJK2TpAkHho95uiTTV6HuUZ8ZTo/4Ia0ydGwsCp7bPDHJycnlgFWJ+nyb+VHCIkV2QC; mobilev=html5; __jdb=122270672.4.1605981630702658105508|3.1608646325; mba_sid=16086463253949403903484851050.4; __jd_ref_cls=JDReact_StartReactModule');
            die;

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

