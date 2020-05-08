<?php

namespace Common\Cache;

use Library\Abstracts\Cache;
use Library\Exception\BadRequestException;
use function Common\DI;

/**
 * 用户 缓存层
 * Class JdSignItem
 * @package Common\Cache
 * @author  LYi-Ho 2018-11-26 09:22:44
 */
class User extends Cache
{
    protected function getTableName()
    {
        return 'user';
    }

    /**
     * 好友 model
     * @return \Common\Model\User
     */
    protected function Model_User()
    {
        return new \Common\Model\User();
    }

    /**
     * 获取会员缓存
     * @param bool|int|\PhalApi\long $user_id
     * @return array|mixed|null
     * @throws BadRequestException
     */
    public function get($user_id = false)
    {
        if (!empty($user_id)) {
            $name = $this->getTableName();
            $user = DI()->cache->get($name);
            if ($user == null) {
                $user = $this->Model_User()->get($user_id);
                if ($user) {
                    $this->set($user_id, $user);
                }
            }
        }
        return $user ?? [];
    }

    /**
     * 更新会员
     * @param $where
     * @param $data
     */
    public function update($where, $data)
    {
        if (is_array($where)) {
            $limit = 100;
            $offset = 0;
            while ($users = $this->Model_User()->getListLimitByWhere($limit, $offset, $where, 'id desc', 'id')) {
                foreach ($users as $user) {
                    $this->Model_User()->update($user['id'], $data);
                    // 删除缓存
                    $this->delete($user['id']);
                }
                $offset += $limit;
            }
            // $this->Model_User()->updateByWhere($where, $data);
        } else {
            $this->Model_User()->update($where, $data);
            // 删除缓存
            $this->delete($where);
        }
    }

}
