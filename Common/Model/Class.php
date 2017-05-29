<?php

class Model_Class extends PhalApi_Model_NotORM {

    public function getClassList($limit, $offset, $where, $select, $order) {
		$rs['total'] = $this->getORM()->where($where)->count();
        $rs['rows'] = $this->getORM()->select($select)->where($where)->order($order)->limit($limit,$offset)->fetchAll();
		return $rs;
    }

    protected function getTableName($id) {
        return 'class';
    }
}
