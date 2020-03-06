<?php


namespace Library\Traits;


use Library\Exception\BadRequestException;
use Library\Tool\ClassInstance;
use function Common\DI;
use function PhalApi\T;

/**
 * 动态调用类方法 实现
 * Trait ClassDynamicCalled
 * @package Library\Traits
 */
trait ClassDynamicCalled
{
    /**
     * 获取指定Domain
     * @param bool $className 指定调用的类
     * @return mixed 返回对应的 Domain实例
     * @throws BadRequestException
     */
    protected static function getDomain($className = false)
    {
        return self::getClass('Domain', $className);
    }

    /**
     * 获取类
     * @param bool $classType
     * @param bool $className
     * @return mixed
     * @throws BadRequestException
     */
    private static function getClass($classType = false, $className = false)
    {
        if (empty($classType)) {
            throw new BadRequestException(T('非法调用'));
        }
        if ($classType != 'Domain') {
            $classInstanceTool = self::ClassInstance()->domainInstance;
        } else if ($classType != 'Model') {
            $classInstanceTool = self::ClassInstance()->modelInstance;
        } else {
            throw new BadRequestException(T('非法调用'));
        }
        // 拆解当前使用的类名
        $classInfo = explode('\\', get_called_class());
        // 当前使用的类名
        $className = empty($className) ? end($classInfo) : $className;
        // 获取类实例
        $classInstance = $classInstanceTool->$className;
        if (isset($classInstance)) {
            // 已创建实例，不再重新创建
            return $classInstance;
        }
        // 使用当前调用的模块命名空间  或  使用的类的命名空间
        $nameSpace = defined('NAME_SPACE') ? NAME_SPACE : reset($classInfo);
        // 拼接成类
        $class = implode('\\', [$nameSpace, $classType, $className]);
        if ($nameSpace != 'Common' && !class_exists($class)) {
            $class = implode('\\', ['Common', $classType, $className]);
        }
        return $classInstanceTool->$className = new $class;
    }

    /**
     * @return ClassInstance
     */
    private static function ClassInstance()
    {
        return DI()->class;
    }

    /**
     * 获取指定Model
     * @param bool $className 指定调用的类
     * @return mixed 返回对应的 Model实例
     * @throws BadRequestException
     */
    protected static function getModel($className = false)
    {
        return self::getClass('Model', $className);

    }
}
