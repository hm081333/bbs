<?php
declare (strict_types=1);

namespace app\admin\controller;

use app\BaseController;
use think\Request;

class Setting extends BaseController
{

    /**
     * 获取配置信息接口
     * @desc 获取配置信息
     * @return false|mixed|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function get()
    {
        $name = $this->request->post('setting_key_name');
        $setting = $this->modelSetting->where('id', 1)->cache()->find();// 从数据库获取
        if ($name !== false) $setting = isset($setting[$name]) ? unserialize($setting[$name]) : false;
        return success('', $setting);
    }


    /**
     * 保存更新的资源
     * @return \think\Response
     */
    public function set()
    {
        $name = $this->request->post('setting_key_name');
        // 获取并序列化 存进数据库的是一个序列
        $data = $this->request->post('setting_key_data', []);
        $setting = $this->modelSetting->where('id', 1)->cache()->find();// 从数据库获取
        $setting->$name = serialize($data);// 替换当前修改的数据
        $setting->save();
        return success('操作成功');
    }

}
