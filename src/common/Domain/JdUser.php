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
        $user = \Common\Domain\User::getCurrentUser(true);
        $modelJdUser = self::getModel();
        if ($data['id']) {
            // 修改
            self::DI()->response->setMsg(\PhalApi\T('修改成功'));
        } else {
            // 添加
            self::DI()->response->setMsg(\PhalApi\T('添加成功'));
            $jd_user_info = self::getDomain('JDSign')::getJDUserInfo($data);
            $insert_data = [
                'user_id' => $user['id'],
                'jd_user_name' => $jd_user_info['user_name'],
                'jd_nick_name' => $jd_user_info['nick_name'],
                'jd_level_name' => $jd_user_info['level_name'],
                'pt_key' => $data['pt_key'],
                'pt_pin' => $data['pt_pin'],
                'pt_token' => $data['pt_token'],
                'status' => 1,
                'add_time' => NOW_TIME,
                'refresh_time' => NOW_TIME,
            ];

            $check = $modelJdUser->getInfo(['jd_user_name' => $insert_data['jd_user_name']], 'id');
            if ($check) {
                throw new \Library\Exception\BadRequestException(\PhalApi\T('该账号已经绑定过了'));
            }
            $insert_rs = $modelJdUser->insert($insert_data);
            if ($insert_rs === false) {
                throw new \Library\Exception\InternalServerErrorException(\PhalApi\T('添加失败'));
            }
        }

    }


}
