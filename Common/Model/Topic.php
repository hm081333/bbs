<?php

class Model_Topic extends PhalApi_Model_NotORM {

	public function getAllTopic($where, $select, $order) {
		return $this->getORM()->select($select)->where($where)->order($order)->fetchAll();
	}

    public function getTopicList($limit, $offset, $where, $select, $order) {
		$rs['total'] = $this->getORM()->where($where)->count();
        $rs['rows'] = $this->getORM()->select($select)->where($where)->order($order)->limit($limit,$offset)->fetchAll();
		return $rs;
    }

	public function TopicCount($where) {
		$rs = $this->getORM()->where($where)->count();
		return $rs;
	}

    protected function getTableName($id) {
        return 'topic';
    }
}
