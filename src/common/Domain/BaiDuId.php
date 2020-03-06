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
use function PhalApi\T;

/**
 * 百度ID 领域层
 * BaiDuId
 * @package Common\Domain
 * @author  LYi-Ho 2018-11-26 11:14:57
 */
class BaiDuId
{
    use Domain;

    /**
     * 删除百度ID - 覆盖Domain的删除函数
     * @param int $id
     * @throws InternalServerErrorException
     */
    public static function delInfo($id)
    {
        self::DI()->response->setMsg(T('删除成功'));
        $del_tieba = self::Model_TieBa()->deleteByWhere(['baidu_id' => $id]);
        $del_baiduid = self::getModel()->delete($id);
        if ($del_tieba === false || $del_baiduid === false) {
            throw new InternalServerErrorException(T('删除失败'));
        }
    }

    /**
     * 贴吧 数据层
     * @return \Common\Model\TieBa|Model|NotORMModel
     */
    protected static function Model_TieBa()
    {
        return self::getModel('TieBa');
    }

    /**
     * 添加百度ID
     * @param $name
     * @param $bduss
     * @throws BadRequestException
     * @throws InternalServerErrorException
     */
    public static function add($name, $bduss)
    {
        self::DI()->response->setMsg(T('添加成功'));
        $user = User::getCurrentUser(true);
        $modelBaiDuId = self::Model_BaiDuId();
        $check = $modelBaiDuId->getInfo(['name' => $name], 'id');
        if ($check) {
            throw new BadRequestException(T('该账号已经绑定过了'));
        }
        $insert_rs = $modelBaiDuId->insert(['user_id' => $user['id'], 'bduss' => $bduss, 'name' => $name]);
        if ($insert_rs === false) {
            throw new InternalServerErrorException(T('添加失败'));
        }
    }

    /**
     * 百度ID 数据层
     * @return \Common\Model\BaiDuId|Model|NotORMModel
     */
    protected static function Model_BaiDuId()
    {
        return self::getModel('BaiDuId');
    }


}
