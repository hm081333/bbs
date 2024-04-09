<?php

namespace App\Utils\TieBa;


use App\Exceptions\Request\BadRequestException;
use App\Exceptions\Request\UnauthorizedException;
use App\Exceptions\Server\InternalServerErrorException;
use App\Models\Tieba\BaiduTieba;
use App\Models\User\User;
use App\Utils\Tools;
use App\Utils\WeChat\OfficialAccount;
use EasyWeChat\Kernel\HttpClient\Response;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HigherOrderCollectionProxy;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * 其他功能类
 */
class Misc
{
    /**
     * 添加BDUSS
     *
     * @param string      $bduss
     * @param string|null $stoken
     *
     * @return \App\Models\Tieba\BaiduId
     * @throws BadRequestException
     * @throws BindingResolutionException
     * @throws UnauthorizedException
     */
    public static function addBduss(string $bduss, ?string $stoken = null)
    {
        $bduss = Utils::parse_bduss($bduss);
        $stoken = empty($stoken) ? null : Utils::parse_bduss($stoken);
        $baiduUserInfo = Utils::getBaiduUserInfo($bduss);
        if (empty($baiduUserInfo['portrait'])) throw new BadRequestException('信息有误，请核验后重新绑定');
        $name = Utils::sqlAdds($baiduUserInfo['name']);
        $portrait = Utils::sqlAdds($baiduUserInfo["portrait"]);
        $user_id = Tools::auth()->id('user');
        $baiduid = Tools::model()->TiebaBaiduId
            ->where('portrait', $portrait)
            ->firstOrNew();
        $attributes = [
            'bduss' => $bduss,
            'stoken' => $stoken,
            'name' => $name,
        ];
        if (!$baiduid->exists) {
            $attributes['user_id'] = $user_id;
            $attributes['portrait'] = $portrait;
        } else if ($baiduid->user_id != $user_id) throw new BadRequestException('该账号已被其他人绑定');
        $baiduid->forceFill($attributes)->save();
        return $baiduid;
    }

    /**
     * 扫描指定用户的所有贴吧并储存--用于一键刷新
     *
     * @param int|null $user_id UserID，如果留空，表示当前用户的UID
     *
     * @throws BadRequestException
     * @throws BindingResolutionException
     * @throws UnauthorizedException
     */
    public function scanTiebaByUser(?int $user_id = null)
    {
        set_time_limit(0);
        if (empty($user_id)) $user_id = Tools::auth()->id('user');
        Tools::model()->TiebaBaiduId->where(['user_id' => $user_id])->select('id')->each(fn($baidu) => static::scanTiebaByPid($baidu['id']), 100);
    }

    /**
     * 扫描指定PID的所有贴吧
     *
     * @param int $pid PID
     *
     * @throws InternalServerErrorException
     */
    public static function scanTiebaByPid($baidu_id)
    {
        set_time_limit(0); // 不超时
        $baiduId = Tools::model()->TiebaBaiduId->find($baidu_id);
        if (empty($baiduId->stoken) && empty($baiduId->bid)) $baiduId->bid = static::getUseridOld($baiduId['bduss']);
        $pn = 1;
        while ($baiduId->stoken || $baiduId->bid) {
            $ngf = collect();
            if ($baiduId->stoken) {
                $rc = self::getTieba2($baiduId->bduss, $baiduId->stoken, $pn);//fetch forum list //default 200 per page
                if (empty($rc)) break;
                if (!empty($rc['data']['like_forum']['list']) && is_array($rc['data']['like_forum']['list'])) $ngf = $ngf->merge($rc['data']['like_forum']['list']);
            } else {
                $rc = static::getTieba($baiduId->bid, $baiduId->bduss, $pn);//fetch forum list //default 200 per page
                if (!empty($rc['forum_list']['non-gconforum']) && is_array($rc['forum_list']['non-gconforum'])) $ngf = $ngf->merge($rc['forum_list']['non-gconforum']);
                if (!empty($rc['forum_list']['gconforum']) && is_array($rc['forum_list']['gconforum'])) $ngf = $ngf->merge($rc['forum_list']['gconforum']);
            }
            if ($ngf->isNotEmpty()) {
                // $refresh_time = $rc['time'];
                $refresh_time = time();
                $tieba_exists = Tools::model()->TiebaBaiduTieba
                    ->withTrashed()
                    ->where(['baidu_id' => $baiduId->id])
                    ->select('tieba')
                    ->pluck('tieba');
                /* @var $wait_insert_tieba Collection */
                $wait_insert_tieba = $ngf
                    ->whereNotIn('name', $tieba_exists)
                    ->whereNotIn('forum_name', $tieba_exists)
                    ->map(function ($item) use ($baiduId, $refresh_time) {
                        return [
                            'baidu_id' => $baiduId->id,
                            'fid' => $item['id'] ?? $item['forum_id'],
                            'user_id' => $baiduId->user_id,
                            'tieba' => addslashes(htmlspecialchars($item['name'] ?? $item['forum_name'])),
                            'refresh_time' => $refresh_time,
                            'created_at' => $refresh_time,
                            'updated_at' => $refresh_time,
                        ];
                    });
                unset($ngf, $tieba_exists);
                $wait_insert_tieba_count = $wait_insert_tieba->count();
                if ($wait_insert_tieba_count) {
                    Tools::model()->TiebaBaiduTieba::insert($wait_insert_tieba->toArray());
                    unset($wait_insert_tieba);
                    $baiduId->refresh_time = $refresh_time;
                }
            } else break;
            $pn++;
        }
        // 百度账号信息有变化：新增贴吧数量大于0，更新账号刷新时间
        if ($baiduId->isDirty()) $baiduId->save();
    }

    /**
     * 获取指定pid用户userid
     */
    public static function getUseridOld($bduss)
    {
        $user = new Curl('http://tieba.baidu.com/i/sys/user_json');
        $user->addCookie(['BDUSS' => $bduss]);
        $re = iconv("GB2312", "UTF-8//IGNORE", $user->get());
        $ur = json_decode($re, true);
        $userid = $ur['creator']['id'];
        return $userid;
    }

    /**
     * 获取指定pid用户userid
     */
    public static function getUserid($baidu_id)
    {
        $ub = Tools::model()->TiebaBaiduId->find($baidu_id);
        return static::getUseridByPortrait($ub['portrait']);
    }

    /**
     * 获取指定portrait的userid
     */
    public static function getUseridByPortrait($portrait)
    {
        $ur = Utils::getUserInfo($portrait, false);
        $userid = (isset($ur["no"]) && $ur["no"] === 0) ? $ur['data']['id'] : 0;
        return $userid;
    }

    /**
     * 获取指定pid
     *
     * @param $userid
     * @param $bduss
     * @param $pn
     *
     * @return mixed
     * @throws \Exception
     */
    public static function getTieba($userid, $bduss, $pn)
    {
        $head = [];
        $head[] = 'Content-Type: application/x-www-form-urlencoded';
        $head[] = 'User-Agent: Mozilla/5.0 (SymbianOS/9.3; Series60/3.2 NokiaE72-1/021.021; Profile/MIDP-2.1 Configuration/CLDC-1.1 ) AppleWebKit/525 (KHTML, like Gecko) Version/3.0 BrowserNG/7.1.16352';
        $tl = new Curl('http://c.tieba.baidu.com/c/f/forum/like', $head);
        $data = [
            'BDUSS' => $bduss,
            'friend_uid' => $userid,
            'page_no' => $pn,
            'page_size' => 200,
        ];
        static::addTiebaSign($data, false);
        $tl->addCookie(['BDUSS' => $bduss]);
        $tl->set(CURLOPT_RETURNTRANSFER, true);
        $rt = $tl->post($data);
        return Tools::jsonDecode($rt);
    }

    public static function getTieba2($bduss, $stoken, $pn = 1)
    {

        $tl = new Curl("https://tieba.baidu.com/mg/o/getForumHome?st=0&pn={$pn}&rn=200", ['User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.102 Safari/537.36']);
        $tl->addCookie(['BDUSS' => $bduss, 'STOKEN' => $stoken]);
        return Tools::jsonDecode($tl->get());
    }

    /**
     * 寻找已缓存的贴吧 FID
     *
     * @param string $kw 贴吧名
     *
     * @return string|boolean FID，如果没有缓存则返回false
     * @throws BindingResolutionException
     */
    public static function findFid($kw)
    {
        $r = Tools::model()->TiebaBaiduTieba->where('tieba', $kw)->where('fid', '>', 0)->first();
        return !empty($r['fid']) ? $r['fid'] : false;
    }

    /**
     * 批量设置贴吧 FID
     *
     * @param string $kw  贴吧名
     * @param string $fid FID
     *
     * @return bool
     * @throws BindingResolutionException
     */
    public static function mSetFid($kw, $fid)
    {
        if (empty($fid)) return false;
        return Tools::model()->TiebaBaiduTieba->where('tieba', $kw)->update(['fid' => $fid]);
    }

    /**
     * 得到贴吧 FID
     *
     * @param string $kw 贴吧名
     *
     * @return string FID
     * @throws BindingResolutionException
     */
    public static function getFid($kw)
    {
        $f = static::findFid($kw);
        if ($f) return $f;
        $ch = new Curl('http://tieba.baidu.com/f/commit/share/fnameShareApi?ie=utf-8&fname=' . urlencode($kw), ['User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 14_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0.1 Mobile/15E148 Safari/604.1', 'Referer: http://tieba.baidu.com/']);
        $r = json_decode($ch->exec(), true);
        return intval($r['no']) === 0 ? $r['data']['fid'] : false;
    }

    /**
     * 发送所有贴吧签到详情
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public static function sendTieBaSignDetailAll()
    {
        Tools::model()->UserUser
            ->whereHas('wechatOfficialAccount')
            ->whereHas('baiduIds')
            ->with([
                'wechatOfficialAccount:user_id,open_id',
            ])
            ->each(fn($user) => static::sendTieBaSignDetail($user));
    }

    /**
     * 发送贴吧签到详情
     *
     * @param User $user
     *
     * @return \EasyWeChat\Kernel\HttpClient\Response|\Symfony\Contracts\HttpClient\ResponseInterface
     * @throws BadRequestException
     * @throws InternalServerErrorException
     * @throws \EasyWeChat\Kernel\Exceptions\BadResponseException
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public static function sendTieBaSignDetail(User $user): ResponseInterface|Response
    {
        if (!$user->exists || empty($user->wechatOfficialAccount->open_id) || empty($user->id)) throw new BadRequestException('非法参数');

        $info = Misc::getSignStatus($user);
        if (!$info) throw new InternalServerErrorException('获取状态失败');
        $response = OfficialAccount::sendTemplateMessage([
            'touser' => $user->wechatOfficialAccount->open_id,
            'template_id' => 'Ogvc_rROWerSHvfgo1IOJIL103bso0H3jLYEAwTuKKg',
            'url' => 'https://bbs2.081333.xyz/tieba',
            // 'miniprogram' => [
            //     'appid' => 'xxxxxxx',
            //     'pagepath' => 'pages/xxx',
            // ],
            'data' => [
                'user_name' => [
                    'value' => $info['user_name'],
                    'color' => '#173177',
                ],
                'greeting' => [
                    'value' => $info['greeting'],
                    'color' => '#173177',
                ],
                'tieba_count' => [
                    'value' => $info['tieba_count'],
                    'color' => '#173177',
                ],
                'success_count' => [
                    'value' => $info['success_count'],
                    'color' => '#173177',
                ],
                'fail_count' => [
                    'value' => $info['fail_count'],
                    'color' => '#173177',
                ],
                'ignore_count' => [
                    'value' => $info['ignore_count'],
                    'color' => '#173177',
                ],
            ],
        ]);
        Log::debug('微信推送结果', $response->toArray(false));

        return $response;
    }

    /**
     * 获取签到状态
     *
     * @param User $user
     *
     * @return array
     * @throws BadRequestException
     */
    public static function getSignStatus(User $user): array
    {
        if (!$user->exists) throw new BadRequestException('非法参数');
        $now_time = Tools::now();
        $day_begin_time = $now_time->startOfDay()->timestamp;
        $day_end_time = $now_time->endOfDay()->timestamp;
        $h = $now_time->format('G');
        if ($h < 11) {
            $greeting = '早上好！';
        } else if ($h < 13) {
            $greeting = '中午好！';
        } else if ($h < 17) {
            $greeting = '下午好！';
        } else {
            $greeting = '晚上好！';
        }
        $total = Tools::model()->TiebaBaiduTieba->where('user_id', $user->id)->count();
        if ($total <= 0) throw new BadRequestException('该用户没有贴吧账号');
        //签到成功
        $success_count = Tools::model()->TiebaBaiduTieba
            ->where('user_id', $user->id)
            ->where('no', 0)
            ->where('status', 0)
            ->where('latest', '>=', $day_begin_time)
            ->where('latest', '<=', $day_end_time)
            ->count();
        //签到失败
        $fail_count = Tools::model()->TiebaBaiduTieba
            ->where('user_id', $user->id)
            ->where('no', 0)
            ->where('status', '>', 0)
            ->where('latest', '>=', $day_begin_time)
            ->where('latest', '<=', $day_end_time)
            ->count();
        //忽略签到
        $no_count = Tools::model()->TiebaBaiduTieba
            ->where('user_id', $user->id)
            ->where('no', '>', 0)
            ->count();
        return [
            'user_name' => $user->user_name,
            'greeting' => $greeting,
            'tieba_count' => $total,
            'success_count' => $success_count,
            'fail_count' => $fail_count,
            'ignore_count' => $no_count,
        ];
    }

    /**
     * 执行全部贴吧用户的签到任务
     *
     * @param bool $is_retry true为尝试重试签到，默认false为签到今天未签到的
     *
     * @return void
     * @throws BindingResolutionException
     */
    public static function doSignAll(bool $is_retry = false)
    {
        set_time_limit(0);
        //处理所有未签到的贴吧
        $main_table = Tools::model()->TiebaBaiduTieba->getTable();
        Tools::model()->TiebaBaiduTieba
            ->leftJoin(Tools::model()->TiebaBaiduId->getTable() . ' AS bid', $main_table . '.baidu_id', '=', 'bid.id')
            // 不忽略签到
            ->where($main_table . '.no', 0)
            ->when(
                $is_retry,
                // 签到状态不为0==签到出错
                fn(Builder $query) => $query->where($main_table . '.status', 0),
                // 今天没有签到
                fn(Builder $query) => $query->where($main_table . '.latest', '<', Tools::now()->startOfDay()->timestamp)
            )
            ->orderBy($main_table . '.id')
            ->select([
                $main_table . '.tieba',
                $main_table . '.id',
                $main_table . '.baidu_id',
                $main_table . '.fid',
                'bid.bduss',
            ])
            ->each(fn(BaiduTieba $item) => static::doSign($item), 100);
    }

    /**
     * 执行一个贴吧用户的签到
     *
     * @param $baidu_id
     *
     * @throws BindingResolutionException
     */
    public static function doSignByBaiDuId($baidu_id)
    {
        set_time_limit(0);
        //处理所有未签到的贴吧
        $main_table = Tools::model()->TiebaBaiduTieba->getTable();
        Tools::model()->TiebaBaiduTieba
            ->leftJoin(Tools::model()->TiebaBaiduId->getTable() . ' AS bid', $main_table . '.baidu_id', '=', 'bid.id')
            // 该贴吧用户
            ->where('bid.id', $baidu_id)
            // 不忽略签到
            ->where($main_table . '.no', 0)
            // 今天没有签到
            ->where($main_table . '.latest', '<', Tools::now()->startOfDay()->timestamp)
            ->orderBy($main_table . '.id')
            ->select([
                $main_table . '.id',
                $main_table . '.tieba',
                $main_table . '.fid',
                'bid.bduss',
            ])
            ->each(fn(BaiduTieba $item) => static::doSign($item), 100);
    }

    /**
     * 执行一个会员的签到
     *
     * @param $user_id
     *
     * @throws \PhalApi\Exception\InternalServerErrorException
     */
    public function doSignByUserId($user_id)
    {
        set_time_limit(0);
        //处理所有未签到的贴吧
        while (true) {
            $tieba_list = $this->Model_TieBa()->queryRows("
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
                    Tools::now()->startOfDay()->timestamp,// 今天没有签到
                    0,// 从0开始取
                    100,// 100条100条循环拿
                ]
            );
            if (empty($tieba_list)) break;
            foreach ($tieba_list as $item) {
                $this->doSign($item['tieba'], $item['id'], $item['bduss'], $item['fid']);
            }
        }
    }

    /**
     * 执行一个贴吧的签到
     *
     * @param BaiduTieba $tieba
     *
     * @return array|mixed
     * @throws BindingResolutionException
     */
    public static function doSignByTieBa(BaiduTieba $tieba)
    {
        // $user_id = Tools::auth()->id('user');
        // $main_table = Tools::model()->TiebaBaiduTieba->getTable();
        // $tieba_info = Tools::model()->TiebaBaiduTieba
        //     ->leftJoin(Tools::model()->TiebaBaiduId->getTable() . ' AS bid', $main_table . '.baidu_id', '=', 'bid.id')
        //     ->where($main_table . '.user_id', $user_id)
        //     ->where('bid.user_id', $user_id)
        //     ->where($main_table . '.id', $tieba_id)
        //     ->select([
        //         $main_table . '.id',
        //         $main_table . '.tieba',
        //         $main_table . '.fid',
        //         'bid.bduss',
        //     ])
        //     ->first();
        // if (empty($tieba_info)) throw new BadRequestException('您没有该贴吧');
        return static::doSign($tieba);
    }

    /**
     * 对一个贴吧执行完整的签到任务
     *
     * @param BaiduTieba $tieba
     *
     * @return BaiduTieba
     * @throws BindingResolutionException
     */
    public static function doSign(BaiduTieba $tieba)
    {
        $again_error_id = 160002; //重复签到错误代码
        $again_error_id_2 = 1101; //特殊的重复签到错误代码！！！签到过快=已签到
        $again_error_id_3 = 1102; //特殊的重复签到错误代码！！！签到过快=已签到
        $kw = addslashes($tieba->tieba);
        $bduss = $tieba->bduss ?? $tieba->baidu->bduss;

        if (empty($tieba->fid)) $tieba->fid = static::getFid($kw);//贴吧唯一ID

        $status_succ = false;
        //三种签到方式依次尝试
        $tbs = static::getTbs($bduss);
        //客户端
        if ($status_succ === false) {
            $r = static::doSign_Client($kw, $tieba->fid, $bduss, $tbs);
            $v = json_decode($r, true);
            if ($v != $r && $v != null) {//decode失败时会直接返回原文或NULL
                $status_succ = empty($v['error_code']) || $v['error_code'] == $again_error_id;
                $tieba->status = $status_succ ? 0 : $v['error_code'];
                $tieba->last_error = $status_succ ? null : $v['error_msg'];
            }
        }
        //手机网页
        if ($status_succ === false) {
            $r = static::doSign_Mobile($kw, $tieba->fid, $bduss, $tbs);
            $v = json_decode($r, true);
            if ($v != $r && $v != null) {//decode失败时会直接返回原文或NULL
                $status_succ = empty($v['no']) || $v['no'] == $again_error_id_2 || $v['no'] == $again_error_id_3;
                $tieba->status = $status_succ ? 0 : $v['no'];
                $tieba->last_error = $status_succ ? null : $v['error'];
            }
        }
        //网页---尽量不用
        if ($status_succ === false) {
            $status_succ = static::doSign_Default($kw, $tieba->fid, $bduss) === true;
            if ($status_succ) {
                $tieba->status = 0;
                $tieba->last_error = null;
            }
        }

        $tieba->latest = Tools::now();
        $tieba->save();
        return $tieba;
    }

    /**
     * 得到BDUSS
     *
     * @param int|string $baidu_id   贴吧用户PID
     * @param bool       $withStoken 是否带有stoken
     *
     * @return array|false|HigherOrderCollectionProxy|mixed|string
     * @throws BindingResolutionException
     */
    public static function getCookie($baidu_id, $withStoken = false)
    {
        if (empty($baidu_id)) return false;
        $baiduid = Tools::model()->TiebaBaiduId->find($baidu_id);
        return $withStoken ? ['bduss' => $baiduid->bduss, 'stoken' => $baiduid->stoken] : $baiduid->bduss;
    }

    /**
     * 得到TBS
     *
     * @param $bduss
     *
     * @return
     */
    public static function getTbs($bduss)
    {
        $ch = new Curl('http://tieba.baidu.com/dc/common/tbs');
        $ch->addcookie("BDUSS=" . $bduss);
        $x = json_decode($ch->exec(), true);
        return $x['tbs'];
    }

    /**
     * 对输入的数组添加客户端验证代码（tiebaclient!!!）
     *
     * @param array $data 数组
     */
    public static function addTiebaSign(&$data, $withClientType = true)
    {
        $data["_client_version"] = "12.22.1.0";
        if ($withClientType) {
            $data["_client_type"] = "4";
        }
        ksort($data);
        $x = '';
        foreach ($data as $k => $v) {
            $x .= $k . '=' . $v;
        }
        $data['sign'] = strtoupper(md5($x . 'tiebaclient!!!'));
    }

    /**
     * 客户端签到
     *
     * @param $kw
     * @param $fid
     * @param $ck
     * @param $tbs
     *
     * @return string
     */
    public static function doSign_Client($kw, $fid, $ck, $tbs)
    {
        $ch = new Curl('http://c.tieba.baidu.com/c/c/forum/sign');
        $ch->addcookie("BDUSS=" . $ck);
        $temp = [
            'BDUSS' => $ck,
            'fid' => $fid,
            'kw' => $kw,
            'tbs' => $tbs,
        ];
        static::addTiebaSign($temp);
        return $ch->post($temp);
    }

    /**
     * 手机网页签到
     *
     * @param $kw
     * @param $fid
     * @param $ck
     * @param $tbs
     *
     * @return string
     * @throws \Exception
     */
    public static function doSign_Mobile($kw, $fid, $ck, $tbs): string
    {
        //没问题了
        $ch = new Curl('http://tieba.baidu.com/mo/q/sign?tbs=' . $tbs . '&kw=' . urlencode($kw) . '&is_like=1&fid=' . $fid, ['User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 14_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0.1 Mobile/15E148 Safari/604.1', 'Referer: http://tieba.baidu.com/f?kw=' . $kw, 'Host: tieba.baidu.com', 'X-Forwarded-For: ' . mt_rand(0, 255) . '.' . mt_rand(0, 255) . '.' . mt_rand(0, 255) . '.' . mt_rand(0, 255), 'Origin: http://tieba.baidu.com', 'Connection: Keep-Alive']);
        $ch->addcookie(['BDUSS' => $ck, 'BAIDUID' => strtoupper(md5(time()))]);
        return $ch->exec();
    }

    /**
     * 网页签到
     *
     * @param $kw
     * @param $fid
     * @param $ck
     *
     * @return bool
     * @throws \Exception
     */
    public static function doSign_Default($kw, $fid, $ck): bool
    {
        $cookie = ['BDUSS' => $ck, 'BAIDUID' => strtoupper(md5(time()))];
        $ch = new Curl('http://tieba.baidu.com/mo/m?kw=' . urlencode($kw) . '&fid=' . $fid, ['User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 14_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0.1 Mobile/15E148 Safari/604.1', 'Referer: http://wapp.baidu.com/', 'Content-Type: application/x-www-form-urlencoded']);
        $ch->addcookie($cookie);
        $s = $ch->exec();
        $ch->close();
        preg_match('/\<td style=\"text-align:right;\"\>\<a href=\"(.*)\"\>签到\<\/a\>\<\/td\>\<\/tr\>/', $s, $s);
        if (isset($s[1])) {
            $ch = new Curl(
                'http://tieba.baidu.com' . $s[1],
                [
                    'Accept: text/html, application/xhtml+xml, */*',
                    'Accept-Language: zh-Hans-CN,zh-Hans;q=0.8,en-US;q=0.5,en;q=0.3',
                    'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 14_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0.1 Mobile/15E148 Safari/604.1',
                ]
            );
            $ch->addcookie($cookie);
            $ch->exec();
            $ch->close();
            //临时判断解决方案
            $ch = new Curl('http://tieba.baidu.com/mo/m?kw=' . urlencode($kw) . '&fid=' . $fid, ['User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 14_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0.1 Mobile/15E148 Safari/604.1', 'Referer: http://wapp.baidu.com/', 'Content-Type: application/x-www-form-urlencoded']);
            $ch->addcookie($cookie);
            $s = $ch->exec();
            $ch->close();
            //如果找不到这段html则表示没有签到则stripos()返回false，同时is_bool()返回true，最终返回false
            return !is_bool(stripos($s, '<td style="text-align:right;"><span >已签到</span></td>'));
        } else {
            return true;
        }
    }

    /**
     * 获得二维码及sign
     *
     * @return array
     */
    public static function getLoginQrcode()
    {
        $resp = ['sign' => null, 'imgurl' => null];
        $get_qrcode = json_decode((new Curl('https://passport.baidu.com/v2/api/getqrcode?lp=pc'))->get(), true);
        if (isset($get_qrcode['imgurl']) && isset($get_qrcode['sign'])) $resp = ['sign' => $get_qrcode['sign'], 'imgurl' => $get_qrcode['imgurl']];
        return $resp;
    }

    public static function qrLogin(string $sign)
    {
        $loginResult = static::getRealBduss($sign);
        if ($loginResult["error"] == 0) {
            $baiduUserInfo = Utils::getBaiduUserInfo($loginResult["bduss"]);
            if (!empty($baiduUserInfo["portrait"])) {
                $baidu_name = $baiduUserInfo["name"];
                $baidu_name_portrait = Utils::sqlAdds($baiduUserInfo["portrait"]);
                $user_id = Tools::auth()->id('user');
                $baiduid = Tools::model()->TiebaBaiduId
                    ->where('portrait', $baidu_name_portrait)
                    ->firstOrNew();
                $attributes = [
                    'bduss' => $loginResult["bduss"],
                    'stoken' => $loginResult["stoken"],
                    'name' => $baidu_name,
                ];
                if ($baiduid->exists) {
                    if ($baiduid->user_id != $user_id) throw new BadRequestException('该账号已被其他人绑定');
                    $loginResult["msg"] = "更新BDUSS成功";
                } else {
                    $attributes['user_id'] = $user_id;
                    $attributes['portrait'] = $baidu_name_portrait;
                    $loginResult["msg"] = "获取BDUSS成功";
                }
                $baiduid->forceFill($attributes)->save();
                $loginResult['info'] = $baiduid;
                $loginResult["name"] = "{$baidu_name} [{$baidu_name_portrait}]";
            }
        }
        return $loginResult;
    }

    public static function getRealBduss(string $sign)
    {
        //status code
        //errno不等于0或1时需要要求更换二维码及sign
        //-1 更换二维码
        //0 进入下一步
        //1 无需操作
        //2 已确认
        $r = ["error" => 1, "bduss" => "", "stoken" => "", "msg" => ""];
        $response = (new Curl("https://passport.baidu.com/channel/unicast?channel_id={$sign}&callback="))->get();
        if ($response) {
            $responseParse = json_decode(str_replace(["(", ")"], '', $response), true);
            if (!$responseParse["errno"]) {
                $channel_v = json_decode($responseParse["channel_v"], true);
                if ($channel_v["status"]) {
                    $r["error"] = -1;
                    $r["msg"] = "Continue";
                } else {
                    $s_bduss = json_decode(preg_replace("/'([^'']+)'/", '"$1"', str_replace("\\&", "&", (new Curl('https://passport.baidu.com/v3/login/main/qrbdusslogin?bduss=' . $channel_v["v"]))->get())), true);
                    if ($s_bduss && $s_bduss["code"] === "110000") {
                        $r["error"] = 0;
                        $r["msg"] = "Success";
                        $r["bduss"] = $s_bduss["data"]["session"]["bduss"];
                        $r["stoken"] = self::qrloginParseStoken($s_bduss["data"]["session"]["stokenList"])["tb"];
                    }
                }
            } else {
                $r["error"] = $responseParse["errno"];
            }
        } else {
            $r["error"] = -2;
            $r["msg"] = "Invalid QR Code";
        }
        return $r;
    }

    public static function qrloginParseStoken(string $stokenList)
    {
        $tmpStokenList = [];
        foreach (json_decode(str_replace("&quot;", '"', $stokenList), true) as $stoken) {
            preg_match("/([\w]+)#(.*)/", $stoken, $tmpStoken);
            $tmpStokenList[$tmpStoken[1]] = $tmpStoken[2];
        }
        return $tmpStokenList;
    }

}
