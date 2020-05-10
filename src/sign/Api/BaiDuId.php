<?php

namespace Sign\Api;

/**
 * 百度ID 接口服务类
 * BaiDuId
 * @author LYi-Ho 2018-11-24 16:06:44
 */
class BaiDuId extends \Common\Api\BaiDuId
{
    public function getRules()
    {
        $rules = parent::getRules();
        return $rules;
    }

    /**
     * 列表数据
     * @desc      获取列表数据
     * @return array    数据列表
     * @exception 400 非法请求，参数传递错误
     */
    public function listData()
    {
        $data = get_object_vars($this);
        $data['where']['user_id'] = $this->session_user['id'];
        return self::getDomain()::getList($data['limit'], $data['offset'], $data['where'], $data['field'], $data['order']);
    }

    /**
     * 列表数据 不分页
     * @desc      获取列表数据 不分页
     * @return array    数据列表
     * @exception 400 非法请求，参数传递错误
     */
    public function allListData()
    {
        $data = get_object_vars($this);
        $data['where']['user_id'] = $this->session_user['id'];
        return self::getDomain()::getListByWhere($data['where'], $data['field'], $data['order']);
    }

}
