<?php

class Model_User extends PhalApi_Model_NotORM {

    public function getAllUsers($where, $select, $order) {
        return $this->getORM()->select($select)->where($where)->order($order)->fetchAll();
    }

	public function getUserList($limit, $offset, $where, $select, $order) {
		$rs['total'] = $this->getORM()->where($where)->count();
		$rs['rows'] = $this->getORM()->select($select)->where($where)->order($order)->limit($limit,$offset)->fetchAll();
		return $rs;
	}

    protected function getTableName($id) {
        return 'user';
    }
}
