<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2018-11-26
 * Time: 11:14:57
 */

namespace Common\Domain;

/**
 * 京东账号 领域层
 * JdUser
 * @package Common\Domain
 * @author  LYi-Ho 2018-11-26 11:14:57
 */
class JdUser
{
    use Common;

    public static function statusNames($status = false)
    {
        $names = [
            0 => '未启用',
            1 => '正常',
            2 => '已过期',
        ];
        if ($status === false) {
            return $names;
        }
        return $names[$status];
    }

    /**
     * 添加、修改京东账号
     * @param array $data
     * @throws \Library\Exception\BadRequestException
     * @throws \Library\Exception\InternalServerErrorException
     */
    public static function doInfo($data)
    {
        if (empty($data['pt_key']) || empty($data['pt_pin']) || empty($data['pt_token'])) {
            throw new \Library\Exception\BadRequestException(\PhalApi\T('非法请求'));
        }
        // 该账号的id
        $jd_user_id = $data['id'];
        // 当前登录会员信息
        $user = \Common\Domain\User::getCurrentUser(true);
        $modelJdUser = self::getModel();
        // 获取京东用户信息
        $jd_user_info = self::getDomain('JdSign')::getJDUserInfo($data);
        $data = [
            'jd_user_name' => $jd_user_info['user_name'],
            'jd_nick_name' => $jd_user_info['nick_name'],
            'jd_level_name' => $jd_user_info['level_name'],
            'pt_key' => $data['pt_key'],
            'pt_pin' => $data['pt_pin'],
            'pt_token' => $data['pt_token'],
        ];
        if ($jd_user_id) {
            // 修改
            self::DI()->response->setMsg(\PhalApi\T('修改成功'));

            $check = $modelJdUser->getInfo(['id' => $jd_user_id, 'user_id' => $user['id']], 'id');
            if (!$check) {
                throw new \Library\Exception\BadRequestException(\PhalApi\T('不存在该账号信息'));
            }

            $update_data = array_merge($data, [
                'refresh_time' => NOW_TIME,
            ]);
            $insert_rs = $modelJdUser->update($jd_user_id, $update_data);
            if ($insert_rs === false) {
                throw new \Library\Exception\InternalServerErrorException(\PhalApi\T('添加失败'));
            }
        } else {
            // 添加
            self::DI()->response->setMsg(\PhalApi\T('添加成功'));

            $check = $modelJdUser->getInfo(['jd_user_name' => $data['jd_user_name']], 'id');
            if ($check) {
                throw new \Library\Exception\BadRequestException(\PhalApi\T('该账号已经绑定过了'));
            }

            $insert_data = array_merge($data, [
                'user_id' => $user['id'],
                'status' => 1,
                'add_time' => NOW_TIME,
                'refresh_time' => NOW_TIME,
            ]);
            $insert_rs = $modelJdUser->insert($insert_data);
            if ($insert_rs === false) {
                throw new \Library\Exception\InternalServerErrorException(\PhalApi\T('添加失败'));
            }
        }

    }


}
