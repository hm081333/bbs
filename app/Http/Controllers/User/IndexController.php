<?php

namespace App\Http\Controllers\User;

use App\Exceptions\Request\BadRequestException;
use App\Exceptions\Request\UnauthorizedException;
use App\Exceptions\Server\InternalServerErrorException;
use App\Http\Controllers\BaseController;
use App\Models\User\User;
use App\Models\User\UserLoginLog;
use App\Utils\Tools;
use EasyWeChat;
use EasyWeChat\Kernel\Exceptions\HttpException;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Throwable;

class IndexController extends BaseController
{
    /**
     * 接口参数规则定义
     *
     * @return \array[][]
     */
    protected function getRules()
    {
        return [
            'register' => [
                'user_name' => ['desc' => '用户名', 'string', 'unique' => [User::class, 'user_name']],
                'mobile' => ['desc' => '手机号', 'mobile', 'unique' => [User::class, 'mobile']],
                'email' => ['desc' => '邮箱', 'email', 'unique' => [User::class, 'email']],
                'password' => ['desc' => '密码', 'required', 'min' => 6],
                // 'code' => ['desc' => '验证码', 'required', 'size' => 6],
            ],
            'login' => [
                'user_name' => ['desc' => '用户名', 'required_without_all' => ['mobile', 'email'], 'string', 'exists' => [User::class, 'user_name']],
                'mobile' => ['desc' => '手机号', 'required_without_all' => ['user_name', 'email'], 'mobile', 'exists' => [User::class, 'mobile']],
                'email' => ['desc' => '邮箱', 'required_without_all' => ['user_name', 'mobile'], 'email', 'exists' => [User::class, 'email']],
                'password' => ['desc' => '密码', 'required', 'string', 'size' => 32],
            ],
            'thirdLogin' => [
                'code' => ['desc' => '临时登录凭证', 'required', 'string'],
                'name' => ['desc' => '第三方名称', 'required', 'in' => ['wechatuser']],
                'user' => ['desc' => '用户信息', 'array'],
                'user.code' => ['desc' => '动态令牌。可通过动态令牌换取用户手机号。', 'string'],
            ],
            'edit' => [
                'user_name' => ['desc' => '用户名', 'string', 'unique' => Rule::unique(User::class)->ignore(Tools::auth()->id('user', false))],
                'avatar' => ['desc' => '头像图片', 'file'],
                'nick_name' => ['desc' => '昵称', 'string'],
                'real_name' => ['desc' => '姓名', 'string'],
                'mobile' => ['desc' => '手机号', 'max' => 11, 'mobile'],
            ],
            'setAvatar' => [
                'file' => [
                    'desc' => '头像图片',
                    'max' => 10 * 1024,
                    'required',
                    'image',
                ],
            ],
        ];
    }

    /**
     * 注册
     *
     * @return JsonResponse
     * @throws BadRequestException
     */
    public function register()
    {
        $params = $this->getParams();
        if (empty($params['user_name'])) $params['user_name'] = $params['mobile'];
        // if (!$this->modelVerificationCode->checkCode($params['mobile'], $params['code'], 'register'))
        //     throw new BadRequestException('验证码错误');
        // unset($params['code']);
        $params['o_pwd'] = encrypt($params['password']);
        $params['password'] = bcrypt(strtoupper(md5($params['password'])));
        $this->modelUserUser->saveData($params);
        return Response::api('注册成功');
    }

    /**
     * 登陆
     *
     * @return mixed
     * @throws BadRequestException
     * @throws BindingResolutionException
     */
    public function login()
    {
        $params = $this->getParams();
        /* @var $user User */
        $user = $this->modelUserUser
            ->whereInput('user_name')
            ->whereInput('mobile')
            ->whereInput('email')
            ->first();
        if ($user['status'] != 1) throw new BadRequestException('状态异常，请联系管理员');
        $token = $user->login($params['password']);
        return Response::api('登录成功', [
            'token' => $token,
        ]);
    }

    /**
     * 退出登录
     *
     * @return JsonResponse
     * @throws BindingResolutionException
     */
    public function logout()
    {
        Tools::auth()->logout('user');
        return Response::api('操作成功');
    }

    /**
     * 刷新Token
     *
     * @return JsonResponse
     * @throws BindingResolutionException
     */
    public function refresh()
    {
        return Response::api('操作成功', [
            'token' => Tools::auth()->refresh('user'),
        ]);
    }

    /**
     * 第三方登录
     *
     * @return JsonResponse
     * @throws BadRequestException
     * @throws HttpException
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function thirdLogin()
    {
        $params = $this->getParams();
        $claims = [];
        if ($params['name'] === 'wechatuser') {
            //region 微信小程序登录
            Log::driver('wechat')->debug('request: ' . Tools::jsonEncode(Request::post()));
            try {
                //region 获取微信用户唯一信息
                $response = EasyWeChat::miniApp()->getUtils()->codeToSession($params['code']);
                Log::driver('wechat')->debug('code2Session: ' . Tools::jsonEncode($response));
                $claims['sik'] = $response['session_key'];// 用于解密小程序API信息
                $user = $this->modelUserUser::firstOrCreate(['openid' => $response['openid']], [
                    //'unionid' => $response['unionid'],// 有开放平台才有该ID
                ]);
                // 新创建的用户模型只包含上面创建的字段，需要刷新获取
                if ($user->wasRecentlyCreated) $user->refresh();
                if (!$user->mobile) {
                    //region 获取手机号
                    if (!empty($params['user']) && !empty($params['user']['code'])) {
                        $getPhoneNumber = EasyWeChat::miniApp()->getClient()->postJson('wxa/business/getuserphonenumber', [
                            'code' => (string)$params['user']['code'],
                        ]);
                        Log::driver('wechat')->debug('getPhoneNumber: ' . $getPhoneNumber->toJson());
                        //$getPhoneNumber['phone_info']['phoneNumber']; //用户绑定的手机号（国外手机号会有区号）
                        //$getPhoneNumber['phone_info']['purePhoneNumber'];// 没有区号的手机号
                        if (!empty($getPhoneNumber['phone_info']) && !empty($getPhoneNumber['phone_info']['phoneNumber'])) {
                            $user->mobile = $getPhoneNumber['phone_info']['phoneNumber'];
                            $user->save();
                        }
                    }
                    //endregion
                }
                //endregion
            } catch (Exception $e) {
                Log::driver('wechat')->error($e->getMessage());
                throw new BadRequestException('登录失败');
            }
            //endregion
        } else {
            throw new BadRequestException('暂不支持该第三方登录');
        }
        if ($user['status'] != 1) throw new BadRequestException('状态异常，请联系管理员');
        //region 获取登录令牌
        $token = $this->getUserGuard()->claims($claims)->login($user);
        if (!$token) throw new BadRequestException('登录失败');
        //endregion
        //region 写入登录日志
        UserLoginLog::create(['user_id' => $user->id,]);
        //endregion
        //region 更新最后登录时间
        $user->last_login_time = Tools::now();
        $user->save();
        //endregion
        return Response::api('登录成功', [
            'user_id' => $user->id,
            'token' => $token,
        ]);
    }

    /**
     * 用户信息
     *
     * @return mixed
     * @throws BindingResolutionException
     * @throws UnauthorizedException
     */
    public function info()
    {
        // $params = $this->getParams();
        $user = Tools::auth()->user('user');
        return Response::api('', $user);
    }

    /**
     * 修改用户资料
     *
     * @return JsonResponse
     * @throws BadRequestException
     * @throws BindingResolutionException
     * @throws UnauthorizedException
     */
    public function edit()
    {
        $user = Tools::auth()->user('user');
        $params = $this->getParams();
        if (isset($params['mobile'])) {
            $mobile = $this->modelUserUser->where('mobile', $params['mobile'])->first(['id']);
            if ($mobile && $mobile->id != $user->id) throw new BadRequestException('手机号不可用');
        }
        $user->saveData($params);
        return Response::api('修改成功');
    }

    /**
     * 上传头像
     *
     * @return mixed
     * @throws BadRequestException
     * @throws BindingResolutionException
     * @throws InternalServerErrorException
     * @throws UnauthorizedException
     */
    public function setAvatar()
    {
        $user = Tools::auth()->user('user');
        $params = $this->getParams();
        $user->avatar = Tools::file()->setUploadedFile($params['file'])->save('user/avatar');
        $user->save();
        return Response::api('修改成功');
    }

    /**
     * 签到
     *
     * @return JsonResponse
     * @throws UnauthorizedException
     * @throws Throwable
     */
    public function checkIn(): JsonResponse
    {
        $msg = '签到成功';
        $user = Tools::auth()->user('user');
        Tools::concurrent(function () use ($user, &$msg) {
            //region 后台配置参数
            $config = $this->modelSystemConfig::getList('platform');
            $user_continuously_check_in_days = (int)($config['user_continuously_check_in_days'] ?? 0);
            $user_check_in_get_integral = (int)($config['user_check_in_get_integral'] ?? 0);
            //endregion
            // 曾经签到过 且 当前请求日期 <= 最后签到时间，表示已签到
            if (!empty($user->last_check_in_time) && Tools::today()->lte($user->last_check_in_time->toDateString())) throw new BadRequestException('今天已签到');
            // 曾经签到过 且 当前请求日期的前一天（昨天） = 最后签到时间，表示连续签到
            if (!empty($user->last_check_in_time) && Tools::today()->subDay()->equalTo($user->last_check_in_time->toDateString())) {
                // 连续签到天数加1
                $user->continuously_check_in_days = $user->continuously_check_in_days + 1;
                // 连续签到总天数加1
                $user->total_continuously_check_in_days = $user->total_continuously_check_in_days + 1;
            } else {
                // 连续签到天数重置为1
                $user->continuously_check_in_days = 1;
                // 连续签到总天数重置为1
                $user->total_continuously_check_in_days = 1;
            }
            // 更新签到天数加1
            $user->check_in_days = $user->check_in_days + 1;
            // 后台设置了签到天数和奖励积分数量，且该会员达到了该签到天数
            if ($user_continuously_check_in_days > 0 && $user_check_in_get_integral > 0) {
                if ($user->check_in_days >= $user_continuously_check_in_days) {
                    // 累加积分
                    $user->integral = $user->integral + $user_check_in_get_integral;
                    // 签到天数 归0
                    $user->check_in_days = 0;
                    $msg .= '，获得' . $user_check_in_get_integral . '积分';
                } else {
                    $msg .= '，再签到' . ($user_continuously_check_in_days - $user->check_in_days) . '天获得' . $user_check_in_get_integral . '积分';
                }
            }
            // 更新签到总天数加1
            $user->total_check_in_days = $user->total_check_in_days + 1;
            // 更新最后签到时间
            $user->last_check_in_time = Tools::now();
            $user->save();
        }, $user['id']);
        return Response::api($msg);
    }

}
