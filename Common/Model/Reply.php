<?php

class Model_Reply extends PhalApi_Model_NotORM {

    public function getReplyList($where, $select, $order) {
		$rs['total'] = $this->getORM()->where($where)->count();
        $rs['rows'] = $this->getORM()->select($select)->where($where)->order($order)->fetchAll();
		return $rs;
    }

	public function ReplyCount($where) {
		$rs = $this->getORM()->where($where)->count();
		return $rs;
	}

    protected function getTableName($id) {
        return 'reply';
    }
}
