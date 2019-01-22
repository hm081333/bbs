<?php

namespace Common\Api;

/**
 * 贴吧 接口服务类
 * TieBa
 * @author LYi-Ho 2018-11-24 16:06:44
 */
class TieBa extends Base
{
    use Common;

    public function getRules()
    {
        $rules = parent::getRules();
        $rules['add'] = [
            'bduss' => ['name' => 'bduss', 'type' => 'string', 'require' => true, 'desc' => "BDUSS"],
        ];
        $rules['doInfo'] = [
            'id' => ['name' => 'id', 'type' => 'int', 'require' => true, 'min' => 1, 'desc' => "ID"],
            'bduss' => ['name' => 'bduss', 'type' => 'string', 'require' => true, 'desc' => "BDUSS"],
        ];
        $rules['doSignByTieBaId'] = [
            'tieba_id' => ['name' => 'tieba_id', 'type' => 'int', 'require' => true, 'desc' => 'tieba表的ID'],
        ];
        $rules['doSignByBaiDuId'] = [
            'baidu_id' => ['name' => 'baidu_id', 'type' => 'int', 'require' => true, 'desc' => 'baiduid表的ID--签到该bduss所有贴吧'],
        ];
        $rules['noSignTieBa'] = [
            'tieba_id' => ['name' => 'tieba_id', 'type' => 'int', 'require' => true, 'desc' => 'tieba表的ID'],
            // 'no' => ['name' => 'no', 'type' => 'enum', 'range' => ['0', '1'], 'require' => TRUE, 'desc' => '是否忽略签到'],
            'no' => ['name' => 'no', 'type' => 'boolean', 'require' => true, 'desc' => '是否忽略签到'],
        ];
        $rules['refreshTieBa'] = [
            'baidu_id' => ['name' => 'baidu_id', 'type' => 'int', 'require' => true, 'desc' => 'baiduid表的ID'],
        ];
        $rules['getVCPic'] = [
            'vCodeStr' => ['name' => 'vCodeStr', 'type' => 'string', 'require' => true, 'desc' => '未知'],
        ];
        $rules['sendCode'] = [
            'type' => ['name' => 'type', 'type' => 'string', 'require' => true, 'desc' => '验证码类型'],
            'lstr' => ['name' => 'lstr', 'type' => 'string', 'require' => true, 'desc' => '未知'],
            'ltoken' => ['name' => 'ltoken', 'type' => 'string', 'require' => true, 'desc' => '未知'],
        ];
        $rules['checkVC'] = [
            'user' => ['name' => 'user', 'type' => 'string', 'require' => true, 'desc' => '用户名'],
        ];
        $rules['login'] = [
            'time' => ['name' => 'time', 'type' => 'string', 'require' => true, 'desc' => 'token'],
            'user' => ['name' => 'user', 'type' => 'string', 'require' => true, 'desc' => '用户名'],
            'pwd' => ['name' => 'pwd', 'type' => 'string', 'require' => true, 'desc' => '密码'],
            'p' => ['name' => 'p', 'type' => 'string', 'require' => true, 'desc' => '加密'],
            'vcode' => ['name' => 'vcode', 'type' => 'string', 'require' => true, 'desc' => '验证码'],
            'vcodestr' => ['name' => 'vcodestr', 'type' => 'string', 'require' => true, 'desc' => '验证码'],
        ];
        $rules['login2'] = [
            'type' => ['name' => 'type', 'type' => 'string', 'require' => true, 'desc' => '验证码类型'],
            'lstr' => ['name' => 'lstr', 'type' => 'string', 'require' => true, 'desc' => '未知'],
            'ltoken' => ['name' => 'ltoken', 'type' => 'string', 'require' => true, 'desc' => '未知'],
            'vcode' => ['name' => 'vcode', 'type' => 'string', 'require' => true, 'desc' => '验证码'],
        ];
        $rules['getQRCode'] = [
        ];
        $rules['qrLogin'] = [
            'sign' => ['name' => 'sign', 'type' => 'string', 'require' => true, 'desc' => '未知'],
        ];
        $rules['sendSMS'] = [
            'phone' => ['name' => 'phone', 'type' => 'string', 'require' => true, 'desc' => '手机号'],
            'vcode' => ['name' => 'vcode', 'type' => 'string', 'require' => true, 'desc' => '未知'],
            'vcodestr' => ['name' => 'vcodestr', 'type' => 'string', 'require' => true, 'desc' => '未知'],
            'vcodesign' => ['name' => 'vcodesign', 'type' => 'string', 'require' => true, 'desc' => '未知'],
        ];
        $rules['login3'] = [
            'phone' => ['name' => 'phone', 'type' => 'string', 'require' => true, 'desc' => '手机号'],
            'smsvc' => ['name' => 'smsvc', 'type' => 'string', 'require' => true, 'desc' => '未知'],
        ];
        $rules['getPhone'] = [
            'phone' => ['name' => 'phone', 'type' => 'string', 'require' => true, 'desc' => '手机号'],
        ];
        $rules['getQqQrCode'] = [
        ];
        $rules['qqQrLogin'] = [
            'qrsig' => ['name' => 'qrsig', 'type' => 'string', 'require' => true, 'desc' => '未知'],
        ];
        $rules['getQqLoginUrl'] = [
            'image' => ['name' => 'image', 'type' => 'string', 'require' => true, 'desc' => 'BASE64图片'],
        ];
        $rules['getWxQrCode'] = [
        ];
        $rules['wxQrLogin'] = [
            'uuid' => ['name' => 'uuid', 'type' => 'string', 'require' => true, 'desc' => 'UUID'],
            'last' => ['name' => 'last', 'type' => 'string', 'default' => '', 'desc' => '未知'],
        ];
        return $rules;
    }

    /**
     * 手动添加BDUSS
     * @return mixed
     */
    public function add()
    {
        return self::getDomain()::addBduss($this->bduss);
    }

    /**
     * 更新BDUSS
     * @throws \Exception\BadRequestException
     */
    public function doInfo()
    {
        $data = [
            'id' => $this->id,
            'bduss' => $this->bduss,
        ];
        self::getDomain()::doUpdate($data);
    }

    /**
     * 单个贴吧签到
     */
    public function doSignByTieBaId()
    {
        \PhalApi\DI()->response->setMsg(\PhalApi\T('签到成功'));
        return self::getDomain()::doSignByTieBaId($this->tieba_id);
    }

    /**
     * 账号所有贴吧签到
     */
    public function doSignByBaiDuId()
    {
        \PhalApi\DI()->response->setMsg(\PhalApi\T('签到成功'));
        self::getDomain()::doSignByBaiDuId($this->baidu_id);
    }

    /**
     * 忽略签到
     */
    public function noSignTieBa()
    {
        self::getDomain()::doUpdate([
            'id' => $this->tieba_id,
            'no' => intval($this->no),
        ]);
    }

    /**
     * 刷新贴吧列表
     */
    public function refreshTieBa()
    {
        \PhalApi\DI()->response->setMsg(\PhalApi\T('刷新成功'));
        self::getDomain()::scanTiebaByPid($this->baidu_id);
    }

    /**
     * 拉取登录验证码
     */
    public function getVCPic()
    {
        $data = get_object_vars($this);
        //直接输出图片
        // header('content-type:image/jpeg');
        exit(self::getDomain()::getVCPic($data['vCodeStr']));
    }

    /**
     * 发送短信验证码
     * @return mixed
     */
    public function sendCode()
    {
        $data = get_object_vars($this);
        return self::getDomain()::sendCode($data['type'], $data['lstr'], $data['ltoken']);
    }

    /**
     * 检测登录是否需要验证码
     * @return mixed
     */
    public function checkVC()
    {
        return self::getDomain()::checkVC($this->user);
    }

    /**
     * 获取Token
     * @return mixed
     */
    public function time()
    {
        return self::getDomain()::serverTime();
    }

    /**
     * 登录
     * @return mixed
     */
    public function login()
    {
        return self::getDomain()::login($this->time, $this->user, $this->pwd, $this->p, $this->vcode, $this->vcodestr);
    }

    /**
     * 登录
     * @return mixed
     */
    public function login2()
    {
        return self::getDomain()::login2($this->type, $this->lstr, $this->ltoken, $this->vcode);
    }

    /**
     * 登录二维码
     * @return mixed
     */
    public function getQRCode()
    {
        return self::getDomain()::getQRCode();
    }

    /**
     * 二维码登录
     * @return mixed
     */
    public function qrLogin()
    {
        return self::getDomain()::qrLogin($this->sign);
    }

    /**
     * 发送手机登录短信
     * @return mixed
     */
    public function sendSMS()
    {
        return self::getDomain()::sendSms($this->phone, $this->vcode, $this->vcodestr, $this->vcodesign);
    }

    /**
     * 手机号登录
     * @return mixed
     */
    public function login3()
    {
        return self::getDomain()::login3($this->phone, $this->smsvc);
    }

    /**
     * 检测手机号是否存在
     * @return mixed
     */
    public function getPhone()
    {
        return self::getDomain()::getPhone($this->phone);
    }

    /**
     * 获取QQ登录二维码
     * @return array
     */
    public function getQqQrCode()
    {
        return \TieBa\Domain\QQLogin::getQqQrCode();
    }

    /**
     * 获取QQ二维码登录状态
     * @throws \Exception\BadRequestException
     * @throws \Exception\Exception
     * @throws \Exception\InternalServerErrorException
     */
    public function qqQrLogin()
    {
        return \TieBa\Domain\QQLogin::qrLogin($this->qrsig);
    }

    /**
     * 跳转QQ APP 登录
     * @return array
     * @throws \Exception\InternalServerErrorException
     */
    public function getQqLoginUrl()
    {
        return \TieBa\Domain\QQLogin::getQqLoginUrl($this->image);
    }

    /**
     * 获取微信登录二维码
     * @return array
     */
    public function getWxQrCode()
    {
        return \TieBa\Domain\QQLogin::getWxQrCode();
    }

    /**
     * 获取微信二维码登录状态
     * @return array
     */
    public function wxQrLogin()
    {
        return \TieBa\Domain\QQLogin::wxLogin($this->uuid, $this->last);
    }

}
