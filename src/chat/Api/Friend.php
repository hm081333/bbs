<?php

namespace Chat\Api;

use Library\Traits\Api;

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
     * @throws \Library\Exception\BadRequestException
     */
    protected function Domain_Friend()
    {
        return self::getDomain();
    }

    public function listData()
    {
        $user = $this->Domain_User()::getCurrentUser(true);
        $where = $this->where;
        $where['user_id'] = $user['id'];
        $list = $this->Domain_Friend()::getList($this->limit, $this->offset, $where, $this->field, $this->order);
        var_dump($list);
        return $list;
    }

}
