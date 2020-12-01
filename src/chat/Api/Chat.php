<?php

namespace Chat\Api;

use Library\DateHelper;
use Library\Exception\BadRequestException;
use function Common\res_path;
use function PhalApi\T;

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
        $rules['infoData'] = [
            'id' => ['name' => 'id', 'type' => 'int', 'require' => true, 'min' => 1, 'desc' => "查询ID"],
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
        return self::getDomain('Chat');
    }

    /**
     * 聊天 领域层
     * @return \Common\Domain\ChatMessage
     * @throws BadRequestException
     */
    protected function Domain_ChatMessage()
    {
        return self::getDomain('ChatMessage');
    }

    /**
     * 聊天 模型层
     * @return \Common\Model\ChatMessage
     * @throws BadRequestException
     */
    protected function Model_ChatMessage()
    {
        return self::getModel('ChatMessage');
    }

    /**
     * 聊天 缓存层
     * @return \Common\Cache\Chat
     * @throws BadRequestException
     */
    protected function Cache_Chat()
    {
        return self::getCache('Chat');
    }

    /**
     * 好友 领域层
     * @return \Common\Domain\Friend
     * @throws BadRequestException
     */
    protected function Domain_Friend()
    {
        return self::getDomain('Friend');
    }

    /**
     * 聊天室列表
     * @return array|mixed
     * @throws BadRequestException
     */
    public function listData()
    {
        $user = $this->Domain_User()::getCurrentUser(true);
        $where = $this->where;
        // $where['user_id'] = $user['id'];
        $where['FIND_IN_SET(?, user_ids)'] = $user['id'];
        // $order = $this->order;
        $order = 'last_time DESC';
        $list = $this->Domain_Chat()::getList($this->limit, $this->offset, $where, $this->field, $order);
        foreach ($list['rows'] as &$row) {
            // 聊天室信息
            $chat_info = $row;
            // 最后消息时间
            $last_time = $chat_info['last_time_unix'] ?? $chat_info['last_time'];
            $row = [
                'id' => $chat_info['id'],
            ];
            if ($chat_info['is_group']) {
                // 群聊名称
                $row['name'] = $chat_info['name'];
                $row['logo'] = '';
            } else {
                $friend_info = $this->Domain_Friend()::getInfoByWhere([
                    'user_id' => $user['id'],
                    'FIND_IN_SET(friend_id, ?)' => $chat_info['user_ids'],
                ], 'friend_id,remark_name');
                $friend_remark_name = $friend_info['remark_name'];
                $friend_info = $this->Cache_User()->get($friend_info['friend_id']);
                $row['name'] = empty($friend_remark_name) ? $friend_info['nick_name'] : $friend_remark_name;
                $row['logo'] = empty($friend_info['logo']) ? '' : res_path($friend_info['logo']);

            }
            $last_message = $this->Model_ChatMessage()->getListLimitByWhere(1, 0, ['chat_id' => $chat_info['id']], 'id desc', 'message');
            $row['last_message'] = $last_message[0]['message'] ?? '';
            $row['last_message'] = \Common\strToHtml($row['last_message']);
            if ($warp = strpos($row['last_message'], '<br/>')) {
                $row['last_message'] = substr($row['last_message'], 0, $warp);
            }
            // 最后消息时间
            if (date('Ymd', $last_time) == date('Ymd', time())) {
                // 当天
                $row['last_time_short'] = date('A h:i', $last_time);
                $pat = ['AM', 'PM'];
                $string = ['上午', '下午'];
                $row['last_time_short'] = str_replace($pat, $string, $row['last_time_short']);
            } else if ($last_time >= strtotime(date('Y-m-d') . ' -1 day')) {
                // 昨天
                $row['last_time_short'] = '昨天';
            } else if ($last_time >= strtotime(date('Y-m-d') . ' -6 day')) {
                // 近一周
                $row['last_time_short'] = DateHelper::getWeekName($last_time);
            } else {
                // 更早
                $row['last_time_short'] = date('Y/n/j', $last_time);
            }
        }
        unset($row, $chat_info);
        return $list;
    }

    /**
     * 聊天室信息
     * @desc      获取详情数据
     * @return array    数据数组
     * @exception 400 非法请求，参数传递错误
     */
    public function infoData()
    {
        $user = $this->Domain_User()::getCurrentUser(true);
        $chat_info = $this->Cache_Chat()->get($this->id);
        if (!$chat_info) {
            throw new BadRequestException(T('不存在该聊天室'));
        }
        if ($chat_info['is_group']) {
            // 群聊名称
            // $chat_info['logo'] = '';
        } else {
            $friend_info = $this->Domain_Friend()::getInfoByWhere([
                'user_id' => $user['id'],
                'FIND_IN_SET(friend_id, ?)' => $chat_info['user_ids_str'],
            ], 'friend_id,remark_name');
            $friend_info = $this->Cache_User()->get($friend_info['friend_id']);
            $chat_info['name'] = empty($friend_info['remark_name']) ? $friend_info['nick_name'] : $friend_info['remark_name'];
            // $chat_info['logo'] = empty($friend_info['logo']) ? '' : res_path($friend_info['logo']);
        }
        return $chat_info;
    }

}
