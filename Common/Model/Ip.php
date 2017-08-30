<?php

class Model_Ip extends PhalApi_Model_NotORM {

    public function getAllIps() {
        return $this->getORM()->select('*')->fetchAll();
    }

	public function getIpList($limit, $offset, $where, $select, $order) {
		$rs['total'] = $this->getORM()->where($where)->count();
		$rs['rows'] = $this->getORM()->select($select)->where($where)->order($order)->limit($limit,$offset)->fetchAll();
		return $rs;
	}

    protected function getTableName($id) {
        return 'ip';
    }
}
