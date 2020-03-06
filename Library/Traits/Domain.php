<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2018/11/24
 * Time: 17:23
 */

namespace Library\Traits;

use Library\Exception\BadRequestException;
use function Common\arr_unix_formatter;
use function Common\DI;
use function PhalApi\T;

/**
 * 领域层 实现
 * Trait Domain
 * @package Library\Traits
 */
trait Domain
{
    use \Library\Traits\ClassDynamicCalled;

    /**
     * 查询数据
     * @param int    $limit  每次查询条数
     * @param int    $offset 开始位置
     * @param array  $where  查询条件
     * @param string $field  字段
     * @param string $order  排序
     * @return array a 返回结果集
     * @return int a.total 总条数
     * @return array a.rows 当前查询结果集
     */
    public static function getList($limit, $offset, $where = [], $field = '*', $order = 'id desc', $count = '*', $id = null)
    {
        $model = self::getModel();
        $list = $model->getList($limit, $offset, $where ?? [], $order, $field, $id, $count);
        array_walk($list['rows'], function (&$value) {
            $value = arr_unix_formatter($value);// 格式化数组中的时间戳
            return $value;
        });
        return $list;
    }

    /**
     * 根据查询条件获取所有数据并分页
     * @param array  $condition
     * @param string $field
     * @return mixed
     */
    public static function getListByWhere($condition = [], $field = '*', $order = 'id desc', $id = null)
    {
        $model = self::getModel();
        return $model->getListByWhere($condition ?? [], $field, $order, $id);
    }

    /**
     * 根据查询条件获取总数量
     * @param array  $where
     * @param string $field
     * @param null   $id
     * @return mixed
     */
    public static function getSum($where = [], $field = '*', $id = null)
    {
        $model = self::getModel();
        return $model->getSum($where ?? [], $field, $id);
    }

    /**
     * 根据查询条件获取总数量
     * @param array  $where
     * @param string $field
     * @param null   $id
     * @return mixed
     */
    public static function getMax($where = [], $field = 'id', $id = null)
    {
        $model = self::getModel();
        return $model->getMax($where ?? [], $field, $id);
    }

    /**
     * 获取信息
     * @param        $id
     * @param string $field
     * @return array|mixed
     */
    public static function getInfo($id, $field = '*')
    {
        if ($id > 0) {
            $model = self::getModel();
            $info = $model->get($id, $field);
            if ($info) {
                $info = arr_unix_formatter($info);// 格式化数组中的时间戳
            }
        } else {
            $info = [];
            $info['id'] = 0;
        }
        return $info;
    }

    /**
     * 获取信息
     * @param array  $where
     * @param string $field
     * @return array|mixed
     */
    public static function getInfoByWhere($where = [], $field = '*')
    {
        if (!$where) {
            $info = [];
            $info['id'] = 0;

        } else {
            $model = self::getModel();
            $info = $model->getInfo($where, $field);
            if ($info) {
                $info = arr_unix_formatter($info);// 格式化数组中的时间戳
            }
        }
        return $info;
    }

    /**
     * 更新或者插入数据
     * @param array $data 更新或者插入的数据
     * @return array
     * @throws BadRequestException 错误抛出异常
     */
    public static function doUpdate($data)
    {
        $model = self::getModel();
        self::DI()->response->setMsg(T('操作成功'));
        if (!empty($data['id'])) {//更新
            $id = $data['id'];
            unset($data['id']);
            $result = $model->update($id, $data);
            $result = $result !== false;
        } else {
            unset($data['id']);
            $result = $model->insert($data);
        }
        if (!$result) {
            throw new BadRequestException(T('操作失败'));
        }
        return ['id' => $result];
    }

    private static function DI()
    {
        return DI();
    }

    /**
     * 根据ID删除数据
     * @param integer $id 删除信息的ID
     * @throws BadRequestException  错误抛出异常
     */
    public static function delInfo($id)
    {
        $model = self::getModel();
        self::DI()->response->setMsg(T('删除成功'));
        $result = $model->delete($id);
        if (!$result) {
            throw new BadRequestException(T('删除失败'));
        }
    }

}
