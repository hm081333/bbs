<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2018/11/24
 * Time: 17:23
 */

namespace Common\Domain;

use PhalApi\Model\NotORMModel;

trait Common
{
    private static function DI()
    {
        return \PhalApi\DI();
    }

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
        $list = $model->getList($limit, $offset, $where, $order, $field, $id, $count);
        array_walk($list['rows'], function (&$value) {
            $value = \Common\arr_unix_formatter($value);// 格式化数组中的时间戳
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
        return $model->getListByWhere($condition, $field, $order, $id);
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
        return $model->getSum($where, $field, $id);
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
        return $model->getMax($where, $field, $id);
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
                $info = \Common\arr_unix_formatter($info);// 格式化数组中的时间戳
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
                $info = \Common\arr_unix_formatter($info);// 格式化数组中的时间戳
            }
        }
        return $info;
    }

    /**
     * 更新或者插入数据
     * @param array $data 更新或者插入的数据
     * @return array
     * @throws \Exception\BadRequestException 错误抛出异常
     */
    public static function doUpdate($data)
    {
        $model = self::getModel();
        \PhalApi\DI()->response->setMsg(\PhalApi\T('操作成功'));
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
            throw new \Exception\BadRequestException(\PhalApi\T('操作失败'));
        }
        return ['id' => $result];
    }

    /**
     * 根据ID删除数据
     * @param integer $id 删除信息的ID
     * @throws \Exception\BadRequestException  错误抛出异常
     */
    public static function delInfo($id)
    {
        $model = self::getModel();
        \PhalApi\DI()->response->setMsg(\PhalApi\T('删除成功'));
        $result = $model->delete($id);
        if (!$result) {
            throw new \Exception\BadRequestException(\PhalApi\T('删除失败'));
        }
    }

    /**
     * 获取指定Domain
     * @param bool $className 指定调用的类
     * @return mixed
     */
    public static function getDomain($className = false)
    {
        $classInfo = explode('\\', __CLASS__);// 拆解当前使用的类名
        $className = empty($className) ? end($classInfo) : $className;// 当前使用的类名
        $class = implode('\\', [NAME_SPACE, 'Domain', $className]);
        if (NAME_SPACE != 'Common' && !class_exists($class)) {
            $class = implode('\\', ['Common', 'Domain', $className]);
        }
        return new $class;
    }

    /**
     * 获取当前Domain对应的Model
     * @param bool $className 指定调用的类
     * @return \Common\Model\Common|\PhalApi\Model\NotORMModel 返回对应的 Model实例
     */
    public static function getModel($className = false)
    {
        /*$class = str_replace('Common', NAME_SPACE, str_replace('Domain', 'Model', __CLASS__));
        if (!class_exists($class)) {
            $class = str_replace(NAME_SPACE, 'Common', $class);
        }*/
        $classInfo = explode('\\', __CLASS__);// 拆解当前使用的类名
        $className = empty($className) ? end($classInfo) : $className;// 当前使用的类名
        $class = implode('\\', [NAME_SPACE, 'Model', $className]);
        if (NAME_SPACE != 'Common' && !class_exists($class)) {
            $class = implode('\\', ['Common', 'Model', $className]);
        }
        return new $class;
    }

}
