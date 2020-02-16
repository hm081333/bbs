<?php

namespace Common\Api;

/**
 * 京东签到 接口服务类
 * JdSign
 * @author LYi-Ho 2018-11-24 16:06:44
 */
class JdSign extends Base
{
    use Common;

    public function getRules()
    {
        $rules = parent::getRules();
        $rules['doInfo'] = [
            'jd_user_id' => ['name' => 'jd_user_id', 'type' => 'int', 'min' => 1, 'require' => true, 'desc' => '京东用户ID'],
            'open_signs' => ['name' => 'open_signs', 'type' => 'array', 'default' => [], 'desc' => '选择的签到项'],
        ];
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
        $list = self::getDomain()::getList($data['limit'], $data['offset'], $data['where'], $data['field'], $data['order']);
        return $list;
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

    /**
     * 更新 会员选择的 京东签到项目
     * @throws \Library\Exception\BadRequestException
     */
    public function doInfo()
    {
        self::getDomain()->doInfo($this->jd_user_id, $this->open_signs);
    }


}
