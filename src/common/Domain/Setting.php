<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2018-11-26
 * Time: 11:14:57
 */

namespace Common\Domain;

use Library\Exception\InternalServerErrorException;
use Library\Traits\Domain;
use function PhalApi\T;

/**
 * 系统配置 领域层
 * Setting
 * @package Common\Domain
 * @author  LYi-Ho 2018-11-26 11:14:57
 */
class Setting
{
    use Domain;

    /**
     * 更新配置
     * @param string $name
     * @param array  $data
     * @throws InternalServerErrorException
     */
    public static function updateSetting(string $name, array $data = [])
    {
        self::DI()->response->setMsg(T('操作成功'));
        $data = serialize($data);// 序列化 存进数据库的是一个序列
        $setting_model = self::getModel();
        $result = $setting_model->update(1, [$name => $data]);
        if ($result === false) {
            throw new InternalServerErrorException(T('更新失败'));
        }
        $setting = self::getSetting();// 获取原有设置参数数据
        $setting[$name] = $data;// 替换当前修改的数据
        self::DI()->cache->set('setting', $setting);// 更新文件缓存
    }

    /**
     * 获取配置
     * @param bool|string $name
     * @return array|bool|mixed
     */
    public static function getSetting($name = false)
    {
        $setting = self::DI()->cache->get('setting');// 从文件缓存获取
        if (!$setting) {// 文件缓存不存在
            $setting_model = self::getModel();
            $setting = $setting_model->get(1);// 从数据库获取
            unset($setting['id']);
            self::DI()->cache->set('setting', $setting);// 生成文件缓存，避免多次读取数据库
        }
        if ($name !== false) {
            return isset($setting[$name]) ? unserialize($setting[$name]) : false;
        }
        return $setting;
    }

}
