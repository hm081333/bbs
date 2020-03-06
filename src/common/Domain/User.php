<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2018-11-26
 * Time: 11:14:57
 */

namespace Common\Domain;

use Library\Exception\BadRequestException;
use Library\Exception\InternalServerErrorException;
use Library\Traits\Domain;
use Library\Traits\Model;
use PhalApi\Model\NotORMModel;
use function Common\decrypt;
use function Common\DI;
use function Common\encrypt;
use function Common\pwd_hash;
use function Common\pwd_verify;
use function PhalApi\T;

/**
 * 用户 领域层
 * Class User
 * @package Common\Domain
 * @author  LYi-Ho 2018-11-26 11:14:57
 */
class User
{
    use Domain;

    public static $user;
    public static $user_token;

    /**
     * 用户登录
     * @param $data
     * @return mixed
     * @throws BadRequestException
     */
    public static function doSignIn($data)
    {
        DI()->response->setMsg(T('登陆成功'));
        $user_name = $data['user_name'];   // 账号参数
        $password = $data['password'];   // 密码参数
        $remember = $data['remember'];   // 记住登录
        if (empty($user_name)) {
            throw new BadRequestException(T('请输入账号'));// 抛出普通错误 T标签翻译
        } else if (empty($password)) {
            throw new BadRequestException(T('请输入密码'));// 抛出普通错误 T标签翻译
        }
        $user = self::getInfoByWhere(['user_name' => $user_name], '*');
        if (!$user) {
            throw new BadRequestException(T('用户不存在'));// 抛出客户端错误 T标签翻译
        } else if (!pwd_verify($password, $user['password'])) {
            throw new BadRequestException(T('密码错误'));
        } else {
            $update = [];
            if (empty($user['a_pwd'])) {
                $update['a_pwd'] = $user['a_pwd'] = encrypt($password);
            }
            if ($remember) {
                $update['token'] = $user['token'] = md5(USER_TOKEN . md5(uniqid(mt_rand())));
            }
            // 待更新的会员ID
            // $update['id'] = $user['id'];
            // DI()->logger->debug('user update', $update);
            self::Model_user()->update($user['id'], $update);
            // 将用户信息存入SESSION中
            self::setUserToken($user);
            return [
                'user' => self::getCurrentUserInfo($user),
                'token' => $update['token'] ?? '',
            ];
        }
    }

    /**
     * 用户 数据层
     * @return \Common\Model\User|Model|NotORMModel
     */
    public static function Model_user()
    {
        return self::getModel('User');
    }

    /**
     * 获取当前会员信息
     * @param bool $user
     * @return array
     */
    public static function getCurrentUserInfo($user = false)
    {
        $user = !$user ? self::$user : $user;// 传入user或当前登录user
        if (!$user) {
            return [];
        }
        return [
            'user_name' => $user['user_name'],
            'email' => $user['email'],
            'real_name' => $user['real_name'],
            'birth_time' => $user['birth_time_date'],
            'birth_time_unix' => $user['birth_time_unix'],
            'sex' => $user['sex'],
            'sex_name' => self::getSexName($user['sex']),
        ];
    }

    public static function getSexName($type = false)
    {
        $sex = [
            '1' => '男',
            '2' => '女',
        ];
        if ($type !== false) {
            return $sex[$type];
        }
        return $sex;
    }

    /**
     * 用户登出
     */
    public static function doSignOut()
    {
        DI()->response->setMsg(T('退出成功'));
        self::unsetUserToken();
    }

    /**
     * 注销会员登录状态
     */
    public static function unsetUserToken()
    {
        unset($_SESSION[USER_TOKEN]);
    }

    /**
     * 用户注册
     * @param $data
     * @return mixed
     * @throws BadRequestException
     * @throws InternalServerErrorException
     */
    public static function doSignUp($data)
    {
        DI()->response->setMsg(T('注册成功'));
        $user_name = $data['user_name'];   // 账号参数
        // $password = $data['password'];   // 密码参数
        $email = $data['email'];   // 邮箱参数
        // $real_name = $data['real_name'];   // 姓名参数
        // $birth = $data['birth'];   // 生日参数
        // $sex = $data['sex'];   // 性别参数
        $user_model = self::getModel();
        $checkUser = $user_model->getCount(['user_name' => $user_name], 'id');
        if ($checkUser) {
            throw new BadRequestException(T('用户名已注册'));// 抛出客户端错误 T标签翻译
        }
        $checkEmail = $user_model->getCount(['email' => $email], 'id');
        if ($checkEmail) {
            throw new BadRequestException(T('邮箱已注册'));// 抛出客户端错误 T标签翻译
        }

        $insert_data = [
            'user_name' => $data['user_name'],
            'password' => $data['password'],
            'email' => $data['email'],
            'real_name' => $data['real_name'],
            'birth_time' => substr($data['birth_time'], 0, 10),
            'sex' => $data['sex'],
            'reg_time' => time(),
            'status' => 1,
        ];
        $insert_data['a_pwd'] = encrypt($insert_data['password']);
        $insert_data['password'] = pwd_hash($insert_data['password']);
        $insert_id = $user_model->insert($insert_data);

        if (!$insert_id) {
            throw new InternalServerErrorException(T('注册失败'));// 抛出服务端错误 T标签翻译
        }

        $user = self::getInfo($insert_id);// 获取会员信息
        //将用户信息存入SESSION中
        self::setUserToken($user);
        return [
            'user' => self::getCurrentUserInfo($user),
        ];
    }

    /**
     * 取得当前登录会员
     * @param bool $thr
     * @return array|mixed
     * @throws BadRequestException
     */
    public static function getCurrentUser(bool $thr = false)
    {
        if (!isset(self::$user) || IS_CLI) {
            $user = self::getUserToken();// 获取Session中存储的会员信息
            // var_dump($user);
            if (!$user) {
                // $user_token = DI()->request->getHeader(USER_TOKEN,false);// 获取header中携带的Token
                // $auth = \Common\DI()->request->getHeader('Auth', false);// 获取header中携带的Token
                // $user_token = substr($auth, strlen(USER_TOKEN));// 截取Token
                // var_dump(self::$user_token);
                if (!empty(self::$user_token)) {
                    $user = self::getInfoByWhere(['token' => self::$user_token]);// 用Token换取会员信息
                    if ($user) {
                        self::setUserToken($user);
                    }
                }
            }
            self::$user = !$user ? [] : $user;// 获取不到会员时给空，注意不能不赋值
        }
        if ($thr && !self::$user) {
            throw new BadRequestException(T('请登录'), 1);
        }
        return self::$user;
    }

    /**
     * 获取会员登录状态
     * @return mixed
     */
    public static function getUserToken()
    {
        return DI()->serialize->decrypt(decrypt($_SESSION[USER_TOKEN] ?? ''));// Session中的会员信息
    }

    /**
     * 设置会员登录状态
     * @param array $user
     */
    public static function setUserToken(array $user)
    {
        //将用户信息存入SESSION中
        $_SESSION[USER_TOKEN] = encrypt(DI()->serialize->encrypt($user));// 保存在session
    }
}
