<?php

namespace Chat\Api;

use Library\Exception\BadRequestException;
use Library\Traits\Api;
use Library\Pinyin;
use function Common\res_path;

/**
 * 好友模块接口服务
 * Friend
 * @author LYi-Ho 2020-05-08 12:08:17
 */
class Friend extends \Common\Api\Friend
{
    use Api;

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
     * 好友 领域层
     * @return \Common\Domain\Friend
     * @throws BadRequestException
     */
    protected function Domain_Friend()
    {
        return self::getDomain();
    }

    /**
     * 用户 缓存层
     * @return \Common\Cache\User
     * @throws BadRequestException
     */
    protected function Cache_User()
    {
        return self::getCache('User');
    }

    public function listData()
    {
        $pinyin = new Pinyin();
        $user = $this->Domain_User()::getCurrentUser(true);
        $where = $this->where;
        $where['user_id'] = $user['id'];
        // $list = $this->Domain_Friend()::getList($this->limit, $this->offset, $where, $this->field, $this->order);
        $list = $this->Domain_Friend()::getListByWhere($where, 'friend_id');
        foreach ($list as &$row) {
            $friend = $this->Cache_User()->get($row['friend_id']);
            // var_dump($friend);
            $row = [];
            $row['user_id'] = $friend['id'];
            $row['nick_name'] = $friend['nick_name'];
            $row['logo'] = empty($friend['logo']) ? '' : res_path($friend['logo']);
            $row['pinyin'] = $pinyin->str2py($row['nick_name']);
        }
        unset($row);
        return $list;
    }

}
