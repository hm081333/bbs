<?php
declare (strict_types=1);

namespace app\sign\controller;

use app\BaseController;
use library\exception\BadRequestException;
use library\exception\InternalServerErrorException;
use library\tieba\TiebaApi;
use library\tieba\QQLogin;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Request;
use think\response\Json;

class TieBa extends BaseController
{
    /**
     * 添加百度ID
     * @param $name
     * @param $bduss
     * @throws BadRequestException
     * @throws InternalServerErrorException
     */
    private function addBaiDuIdId($name, $bduss)
    {
        $user = $this->request->getCurrentUser(true);
        $check = $this->modelBaiDuId->where(['name' => $name])->find();
        if ($check) throw new BadRequestException('该账号已经绑定过了');
        $insert_rs = $this->modelBaiDuId->insert(['user_id' => $user['id'], 'bduss' => $bduss, 'name' => $name]);
        if ($insert_rs === false) throw new InternalServerErrorException(T('添加失败'));
        return success('添加成功');
    }

    /**
     * 手动添加BDUSS
     * @return mixed
     * @throws BadRequestException
     * @throws InternalServerErrorException
     */
    public function add($bduss)
    {
        $res = (new TiebaApi())->addBduss($bduss);
        return $this->addBaiDuIdId($res['name'], $res['bduss']);
    }

    /**
     * 更新BDUSS
     * @throws BadRequestException
     */
    public function doInfo($id, $bduss)
    {
        $tieba = $this->modelTieBa->where(['id' => $id])->find();
        $tieba->bduss = $bduss;
        $tieba->save();
        return success('操作成功');
    }

    /**
     * 单个贴吧签到
     * @throws BadRequestException
     */
    public function doSignByTieBaId($tieba_id)
    {
        (new TiebaApi())->doSignByTieBaId($tieba_id);
        return success('签到成功');
    }

    /**
     * 账号所有贴吧签到
     */
    public function doSignByBaiDuId($baidu_id)
    {
        (new TiebaApi())->doSignByBaiDuId($baidu_id);
        return success('签到成功');
    }

    /**
     * 忽略签到
     * @param $tieba_id
     * @param $no
     * @return Json
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function noSignTieBa($tieba_id, $no)
    {
        $tieba = $this->modelTieBa->where(['id' => $tieba_id])->find();
        $tieba->no = intval($no);
        $tieba->save();
        return success('操作成功');
    }

    /**
     * 刷新贴吧列表
     * @throws InternalServerErrorException
     */
    public function refreshTieBa($baidu_id)
    {
        (new TiebaApi())->scanTiebaByPid($baidu_id);
        return success('刷新成功');
    }

    /**
     * 拉取登录验证码
     * @throws InternalServerErrorException
     */
    public function getVCPic($vCodeStr)
    {
        //直接输出图片
        // header('content-type:image/jpeg');
        exit((new TiebaApi())->getVCPic($vCodeStr));
    }

    /**
     * 发送短信验证码
     * @param $type string 验证码类型
     * @param $lstr
     * @param $ltoken
     * @return mixed
     */
    public function sendCode($type, $lstr, $ltoken)
    {
        return success('', (new TiebaApi())->sendCode($type, $lstr, $ltoken));
    }

    /**
     * 检测登录是否需要验证码
     * @return mixed
     * @throws BadRequestException
     * @throws InternalServerErrorException
     */
    public function checkVC($user)
    {
        return success('', (new TiebaApi())->checkVC($user));
    }

    /**
     * 获取Token
     * @return mixed
     * @throws InternalServerErrorException
     */
    public function time()
    {
        return success('', (new TiebaApi())->serverTime());
    }

    /**
     * 登录
     * @param $time string Token
     * @param $user
     * @param $pwd
     * @param $p string 加密
     * @param $vcode
     * @param $vcodestr
     * @return mixed
     * @throws InternalServerErrorException
     */
    public function login($time, $user, $pwd, $p, $vcode, $vcodestr)
    {
        $res = (new TiebaApi())->login($time, $user, $pwd, $p, $vcode, $vcodestr);
        if ($res['code'] != 0) return success('', $res);
        $this->addBaiDuIdId($res['data']['name'], $res['data']['bduss']);
        return success('', $res);
    }

    /**
     * 登录
     * @return mixed
     * @throws BadRequestException
     * @throws InternalServerErrorException
     */
    public function login2($type, $lstr, $ltoken, $vcode)
    {
        $res = (new TiebaApi())->login2($type, $lstr, $ltoken, $vcode);
        if ($res['code'] != 0) return success('', $res);
        $this->addBaiDuIdId($res['data']['name'], $res['data']['bduss']);
        return success('', $res);
    }

    /**
     * 登录二维码
     * @return mixed
     * @throws InternalServerErrorException
     */
    public function getQRCode()
    {
        return success('', (new TiebaApi())->getQRCode());
    }

    /**
     * 二维码登录
     * @return mixed
     * @throws BadRequestException
     * @throws InternalServerErrorException
     */
    public function qrLogin($sign)
    {
        $res = (new TiebaApi())->qrLogin($sign);
        return $this->addBaiDuIdId($res['name'], $res['bduss']);
    }

    /**
     * 发送手机登录短信
     * @return mixed
     * @throws BadRequestException
     * @throws InternalServerErrorException
     */
    public function sendSMS($phone, $vcode, $vcodestr, $vcodesign)
    {
        return success('', (new TiebaApi())->sendSms($phone, $vcode, $vcodestr, $vcodesign));
    }

    /**
     * 手机号登录
     * @return mixed
     * @throws BadRequestException
     * @throws InternalServerErrorException
     */
    public function login3($phone, $smsvc)
    {
        $res = (new TiebaApi())->login3($phone, $smsvc);
        if ($res['code'] != 0) return success('', $res);
        $this->addBaiDuIdId($res['data']['name'], $res['data']['bduss']);
        return $res;
    }

    /**
     * 检测手机号是否存在
     * @return mixed
     * @throws BadRequestException
     * @throws InternalServerErrorException
     */
    public function getPhone($phone)
    {
        return success('', (new TiebaApi())->getPhone($phone));
    }

    /**
     * 获取QQ登录二维码
     * @return Json
     */
    public function getQqQrCode()
    {
        return success('', (new QQLogin())->getQqQrCode());
    }

    /**
     * 获取QQ二维码登录状态
     * @throws BadRequestException
     * @throws InternalServerErrorException
     */
    public function qqQrLogin($qrsig)
    {
        $res = (new QQLogin())->qrLogin($qrsig);
        return $this->addBaiDuIdId($res['name'], $res['bduss']);
    }

    /**
     * 跳转QQ APP 登录
     * @throws InternalServerErrorException
     */
    public function getQqLoginUrl($image)
    {
        return success('', (new QQLogin())->getQqLoginUrl($image));
    }

    /**
     * 获取微信登录二维码
     * @return Json
     */
    public function getWxQrCode()
    {
        return success('', (new QQLogin())->getWxQrCode());
    }

    /**
     * 获取微信二维码登录状态
     * @param $uuid
     * @param string $last
     * @return Json
     * @throws BadRequestException
     */
    public function wxQrLogin($uuid, $last = '')
    {
        $res = (new QQLogin())->wxLogin($uuid, $last);
        return $this->addBaiDuIdId($res['name'], $res['bduss']);
    }
}
