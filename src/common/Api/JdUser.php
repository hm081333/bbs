<?php

namespace Common\Api;

/**
 * 京东账号 接口服务类
 * JdUser
 * @author LYi-Ho 2018-11-24 16:06:44
 */
class JdUser extends Base
{
    use Common;

    public function getRules()
    {
        $rules = parent::getRules();
        $rules['doInfo'] = [
            'id' => ['name' => 'id', 'type' => 'int', 'require' => true, 'min' => 0, 'desc' => "ID"],
            'pt_key' => ['name' => 'pt_key', 'type' => 'string', 'require' => true, 'desc' => "pt_key"],
            'pt_pin' => ['name' => 'pt_pin', 'type' => 'string', 'require' => true, 'desc' => "pt_pin"],
            'pt_token' => ['name' => 'pt_token', 'type' => 'string', 'require' => true, 'desc' => "pt_token"],
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
        foreach ($list['rows'] as &$row) {
            $row['status_name'] = self::getDomain()::statusNames($row['status']);
            $row['sign_list'] = self::getModel('JdSign')->getListByWhere([
                'user_id' => intval($data['where']['user_id']),
                'jd_user_id' => intval($row['id']),
                'status' => 1,
            ], 'sign_key', 'id asc');
            $row['sign_list'] = array_column($row['sign_list'], 'sign_key');
        }
        unset($row);
        $list['sign_list'] = self::getModel('JdSignItem')->getListByWhere([], '*', 'id asc');
        foreach ($list['sign_list'] as &$item) {
            $item['disabled'] = $item['status'] == 0;
        }
        unset($item);
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
     * 更新京东账号
     * @throws \Library\Exception\BadRequestException
     */
    public function doInfo()
    {
        $data = [
            'id' => $this->id,
            'pt_key' => $this->pt_key,
            'pt_pin' => $this->pt_pin,
            'pt_token' => $this->pt_token,
        ];

        self::getDomain()::doInfo($data);
    }


}
