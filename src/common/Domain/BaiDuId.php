<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2018-11-26
 * Time: 11:14:57
 */

namespace Common\Domain;

/**
 * 百度ID 领域层
 * BaiDuId
 * @package Common\Domain
 * @author  LYi-Ho 2018-11-26 11:14:57
 */
class BaiDuId
{
    use Common;

    /**
     * 删除百度ID - 覆盖Domain的删除函数
     * @param int $id
     * @throws \Library\Exception\InternalServerErrorException
     */
    public static function delInfo($id)
    {
        self::DI()->response->setMsg(\PhalApi\T('删除成功'));
        $del_tieba = self::getModel('TieBa')->deleteByWhere(['baidu_id' => $id]);
        $del_baiduid = self::getModel()->delete($id);
        if ($del_tieba === false || $del_baiduid === false) {
            throw new \Library\Exception\InternalServerErrorException(\PhalApi\T('删除失败'));
        }
    }

    /**
     * 添加百度ID
     * @param $name
     * @param $bduss
     * @throws \Library\Exception\BadRequestException
     * @throws \Library\Exception\InternalServerErrorException
     */
    public static function add($name, $bduss)
    {
        self::DI()->response->setMsg(\PhalApi\T('添加成功'));
        $user = \Common\Domain\User::getCurrentUser(true);
        $modelBaiDuId = self::getModel();
        $check = $modelBaiDuId->getInfo(['name' => $name], 'id');
        if ($check) {
            throw new \Library\Exception\BadRequestException(\PhalApi\T('该账号已经绑定过了'));
        }
        $insert_rs = $modelBaiDuId->insert(['user_id' => $user['id'], 'bduss' => $bduss, 'name' => $name]);
        if ($insert_rs === false) {
            throw new \Library\Exception\InternalServerErrorException(\PhalApi\T('添加失败'));
        }
    }


}
