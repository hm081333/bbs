<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2018-11-26
 * Time: 11:14:57
 */

namespace Common\Domain;

use Library\Traits\Domain;
use Library\Traits\Model;
use PhalApi\Model\NotORMModel;

/**
 * 京东签到项 领域层
 * JdSignItem
 * @package Common\Domain
 * @author  LYi-Ho 2018-11-26 11:14:57
 */
class JdSignItem
{
    use Domain;

    public function statusNames($status = false)
    {
        $names = [
            0 => '关闭',
            1 => '启用',
        ];
        if ($status === false) {
            return $names;
        }
        return $names[$status];
    }

    /**
     * 获取 签到项的key对应的name
     * @param bool|string $key
     * @return array|false|mixed|string|null
     */
    public function itemKeyName($key = false)
    {
        $items = $this->Cache_JdSignItem()->get();
        if (!isset($items)) {
            $items = $this->Model_JdSignItem()->getListByWhere([], '`key`,`name`', 'id asc');
            $items = array_combine(array_column($items, 'key'), array_column($items, 'name'));
            $this->Cache_JdSignItem()->set(false, $items);
        }
        if ($key !== false) {
            return $items[$key] ?? '';
        }
        return $items;
    }

    /**
     * 京东签到项 缓存层
     * @return \Common\Cache\JdSignItem
     */
    protected function Cache_JdSignItem()
    {
        return new \Common\Cache\JdSignItem();
    }

    /**
     * 京东签到项 数据层
     * @return \Common\Model\JdSignItem|Model|NotORMModel
     */
    protected function Model_JdSignItem()
    {
        return self::getModel('JdSignItem');
    }


}
