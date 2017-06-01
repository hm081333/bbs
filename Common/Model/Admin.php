<?php

class Model_Admin extends PhalApi_Model_NotORM {

    public function getAllAdmins() {
        return $this->getORM()->select('*')->fetchAll();
    }

	public function getAdminList($limit, $offset, $where, $select, $order) {
		$rs['total'] = $this->getORM()->where($where)->count();
		$rs['rows'] = $this->getORM()->select($select)->where($where)->order($order)->limit($limit,$offset)->fetchAll();
		return $rs;
	}

    protected function getTableName($id) {
        return 'admin';
    }
}
