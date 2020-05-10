<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2020-03-04
 * Time: 17:23
 */

namespace Library\Abstracts;

use function Common\DI;

abstract class Cache
{
    use \Library\Traits\ClassDynamicCalled;

    /**
     * 获取缓存
     * @param bool|string $key
     * @return mixed|null
     */
    public function get($key = false)
    {
        $name = $this->getTableName();
        if ($key !== false) {
            $name = $name . ':' . $key;
        }
        return DI()->cache->get($name);
    }

    abstract protected function getTableName();

    /**
     * 设置缓存
     * @param bool|string $key
     * @param array       $value
     * @return mixed|null
     */
    public function set($key = false, $value = [])
    {
        $name = $this->getTableName();
        if ($key !== false) {
            $name = $name . ':' . $key;
        }
        DI()->cache->set($name, $value, 86400);
        return true;
    }

    /**
     * 删除缓存
     * @param bool $key
     * @return bool
     */
    public function delete($key = false)
    {
        $name = $this->getTableName();
        if ($key !== false) {
            $name = $name . ':' . $key;
        }
        DI()->cache->delete($name);
        return true;
    }

}
