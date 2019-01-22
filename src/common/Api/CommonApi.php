<?php

namespace Common\Api;

/**
 * 服务类接口
 * @ignore
 * @author LYi-Ho 2018-11-24 16:57:36
 */
trait CommonApi
{
    /**
     * 接口参数规则
     * @return array
     */
    public function commonRules()
    {
        return [
            'listData' => [
                'offset' => ['name' => 'offset', 'type' => 'int', 'default' => 0, 'desc' => "开始位置"],
                'limit' => ['name' => 'limit', 'type' => 'int', 'default' => PAGE_NUM, 'desc' => '数量'],
                'field' => ['name' => 'field', 'type' => 'string', 'default' => '*', 'desc' => '查询字段'],
                'where' => ['name' => 'where', 'type' => 'array', 'default' => [], 'desc' => '查询条件'],
                'order' => ['name' => 'order', 'type' => 'string', 'default' => 'id desc', 'desc' => '排序方式'],
            ],
            'allListData' => [
                'field' => ['name' => 'field', 'type' => 'string', 'default' => '*', 'desc' => '查询字段'],
                'where' => ['name' => 'where', 'type' => 'array', 'default' => [], 'desc' => '查询条件'],
                'order' => ['name' => 'order', 'type' => 'string', 'default' => 'id desc', 'desc' => '排序方式'],
            ],
            'infoData' => [
                'id' => ['name' => 'id', 'type' => 'int', 'require' => true, 'min' => 1, 'desc' => "查询ID"],
                'field' => ['name' => 'field', 'type' => 'string', 'default' => '*', 'desc' => '查询字段'],
            ],
            'delInfo' => [
                'id' => ['name' => 'id', 'type' => 'int', 'require' => true, 'min' => 1, 'desc' => "删除ID"],
            ],
        ];
    }

    /**
     * 列表数据
     * @desc      获取列表数据
     * @return array    数据列表
     * @exception 400 非法请求，参数传递错误
     */
    public function listData()
    {
        return self::getDomain()::getList($this->limit, $this->offset, $this->where, $this->field, $this->order);
    }

    /**
     * 列表数据 不分页
     * @desc      获取列表数据 不分页
     * @return array    数据列表
     * @exception 400 非法请求，参数传递错误
     */
    public function allListData()
    {
        return self::getDomain()::getListByWhere($this->where, $this->field, $this->order);
    }

    /**
     * 详情数据
     * @desc      获取详情数据
     * @return array    数据数组
     * @exception 400 非法请求，参数传递错误
     */
    public function infoData()
    {
        return self::getDomain()::getInfo($this->id, $this->field);
    }

    /**
     * 删除数据
     * @desc 删除数据
     * @throws \Exception\BadRequestException
     */
    public function delInfo()
    {
        self::getDomain()::delInfo($this->id);
    }

    /**
     * 获取当前Api对应的Domain
     * @param bool $className 指定调用的类
     * @return \Common\Domain\Common|\Common\Domain\* 返回对应的 Domain实例
     */
    public static function getDomain($className = false)
    {
        /*$class = str_replace('Common', NAME_SPACE, str_replace('Api', 'Domain', __CLASS__));
        if (!class_exists($class)) {
            $class = str_replace(NAME_SPACE, 'Common', $class);
        }*/
        $classInfo = explode('\\', __CLASS__);// 拆解当前使用的类名
        $className = empty($className) ? end($classInfo) : $className;// 当前使用的类名
        $class = implode('\\', [NAME_SPACE, 'Domain', $className]);
        if (NAME_SPACE != 'Common' && !class_exists($class)) {
            $class = implode('\\', ['Common', 'Domain', $className]);
        }
        return new $class;
    }

    /**
     * 获取当前Api对应的Model
     * @param bool $className 指定调用的类
     * @return \Common\Model\Common|\PhalApi\Model\NotORMModel|\Common\Model\* 返回对应的 Model实例
     */
    public static function getModel($className = false)
    {
        /*$class = str_replace('Common', NAME_SPACE, str_replace('Api', 'Model', __CLASS__));
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
