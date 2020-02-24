<?php

namespace Library\Wechat\Message\Event;

use Common\Domain\TuLing;
use Common\Model\TieBa;
use Common\Model\User;
use EasyWeChat\Kernel\Contracts\EventHandlerInterface;
use EasyWeChat\Kernel\Messages\NewsItem as ReturnNewsItem;
use EasyWeChat\Kernel\Messages\News as ReturnNews;
use EasyWeChat\Kernel\Messages\Text as ReturnText;
use Library\DateHelper;
use function Common\DI;

/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2017/6/23
 * Time: 下午 10:13
 */
class Text implements EventHandlerInterface
{
    public function handle($payload = null)
    {
        if (!isset($payload['Content'])) {
            return 'Access Invalid!';
        }
        DI()->logger->debug('\Library\Wechat\Message\Event\Text', $payload);
        // return;

        //file_put_contents(API_ROOT . '/Config/test.php', "<?php   \nreturn " . var_export($inMessage, true) . ';');
        switch ($payload['Content']) {
            case '贴吧签到情况':
                $user_model = new User();
                $openid = $payload['FromUserName'];
                $user = $user_model->getInfo(['open_id' => $openid], 'id,user_name');
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
                $day_time = DateHelper::getDayTime();
                $tieba_model = new TieBa();
                $total = $tieba_model->getCount([
                    'user_id=?' => $user['id'],
                ]);
                $success_count = $tieba_model->getCount([
                    'user_id=?' => $user['id'],
                    'no=?' => 0,
                    'status=?' => 0,
                    'latest>=?' => $day_time['begin'],
                    'latest<=?' => $day_time['end'],
                ]);//签到成功
                $fail_count = $tieba_model->getCount([
                    'user_id=?' => $user['id'],
                    'no=?' => 0,
                    'status>?' => 0,
                    'latest>=?' => $day_time['begin'],
                    'latest<=?' => $day_time['end'],
                ]);//签到失败
                $no_count = $tieba_model->getCount([
                    'user_id=?' => $user['id'],
                    'no>?' => 0,
                ]);//忽略签到
                $content = '用户' . $user['user_name'] . $greeting . PHP_EOL .
                    '您的贴吧总数量为：' . $total . '个。' . PHP_EOL .
                    '今天签到情况：' . PHP_EOL .
                    '签到成功：' . $success_count . '个，' . PHP_EOL .
                    '签到失败：' . $fail_count . '个，' . PHP_EOL .
                    '忽略签到：' . $no_count . '个';
                return new ReturnText($content);
                break;
            default:
                $ask = TuLing::get($payload['Content']);
                DI()->logger->debug('图灵回复', $ask);
                switch ($ask['code']) {
                    case '100000':
                    case '40007':
                        $outMessage = new ReturnText('');
                        // $outMessage->content = $ask['text'];
                        $outMessage->setAttribute('content', $ask['text']);
                        return $outMessage;
                        break;
                    case '40004':
                        // 亲爱的，当天请求次数已用完。
                        return new ReturnText('亲爱的，当天请求次数已用完。');
                        break;
                    default:
                        if (empty($ask['list'])){
                            $items = [
                                new ReturnNewsItem([
                                    'title' => $payload['Content'],
                                    'description' => $ask['text'],
                                    'url' => $ask['url'],
                                    // 'image'       => $ask['url'],
                                ]),
                            ];
                            return new ReturnNews($items);
                        }
                        $items = [];
                        foreach ($ask['list'] as $key => $rs) {
                            if ($key >= 8) {
                                break;
                            }
                            $title = isset($rs['article']) ? $rs['article'] : $rs['name'];
                            $desc = isset($rs['article']) ? $rs['article'] : $rs['info'];
                            $items[] = new ReturnNewsItem([
                                'title' => $title,
                                'description' => $desc,
                                'url' => $rs['detailurl'],
                                'image' => $rs['icon'],
                            ]);
                        }
                        return new ReturnNews($items);
                        break;
                }
                break;
        }
    }
}
