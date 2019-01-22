<?php

namespace Common\Api;

/**
 * 百度ID 接口服务类
 * BaiDuId
 * @author LYi-Ho 2018-11-24 16:06:44
 */
class BaiDuId extends Base
{
    use Common;

    public function getRules()
    {
        $rules = parent::getRules();
        $rules['doInfo'] = [
            'id' => ['name' => 'id', 'type' => 'int', 'require' => true, 'min' => 1, 'desc' => "ID"],
            'bduss' => ['name' => 'bduss', 'type' => 'string', 'require' => true, 'desc' => "BDUSS"],
        ];
        return $rules;
    }

    /**
     * 更新BDUSS
     * @throws \Exception\BadRequestException
     */
    public function doInfo()
    {
        $data = [
            'id' => $this->id,
            'bduss' => $this->bduss,
        ];
        self::getDomain()::doUpdate($data);
    }


}
