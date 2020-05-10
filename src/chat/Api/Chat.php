<?php

namespace Chat\Api;

use Library\Exception\BadRequestException;
use function Common\res_path;

/**
 * 聊天 接口服务
 * Chat
 * @author LYi-Ho 2020-05-10 21:08:17
 */
class Chat extends \Common\Api\Chat
{
    public function getRules()
    {
        $rules = parent::getRules();
        $rules['listData'] = [
            'offset' => ['name' => 'offset', 'type' => 'int', 'default' => 0, 'desc' => "开始位置"],
            'limit' => ['name' => 'limit', 'type' => 'int', 'default' => PAGE_NUM, 'desc' => '数量'],
            'field' => ['name' => 'field', 'type' => 'string', 'default' => '*', 'desc' => '查询字段'],
            'where' => ['name' => 'where', 'type' => 'array', 'default' => [], 'desc' => '查询条件'],
            'order' => ['name' => 'order', 'type' => 'string', 'default' => 'id desc', 'desc' => '排序方式'],
        ];
        return $rules;
    }

    /**
     * 聊天 领域层
     * @return \Common\Domain\Chat
     * @throws BadRequestException
     */
    protected function Domain_Chat()
    {
        return self::getDomain();
    }

    /**
     * 好友列表
     * @return array|mixed
     * @throws BadRequestException
     */
    public function listData()
    {
        $user = $this->Domain_User()::getCurrentUser(true);
        $where = $this->where;
        // $where['user_id'] = $user['id'];
        $where['FIND_IN_SET(?, user_ids)'] = $user['id'];
        $list = $this->Domain_Chat()::getList($this->limit, $this->offset, $where, $this->field, $this->order);
        foreach ($list['rows'] as &$row) {
            $user_ids = explode(',', $row['user_ids']);
            $row = [
                'chat_user' => [],
            ];
            foreach ($user_ids as $user_id) {
                $row['chat_user'][] = $chat_user = $this->Cache_User()->get($user_id);
                if ($user_id != $user['id']) {
                    $row['name'] = $chat_user['remark_name'] ?? $chat_user['nick_name'];
                    $row['logo'] = empty($chat_user['logo']) ? '' : res_path($chat_user['logo']);
                    $row['last_time'] = '下午 9:33';
                    $row['last_message'] = '冲鸭！复工后不能少的"治愈三宝"，统统享会员超低价！';
                }
            }
        }
        unset($row);
        return $list;
    }

    /**
     * 好友信息
     * @desc      获取详情数据
     * @return array    数据数组
     * @exception 400 非法请求，参数传递错误
     */
    public function infoData()
    {
        return [];
    }

}
