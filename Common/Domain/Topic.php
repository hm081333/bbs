<?php

class Domain_Topic {

    public function getTopicList($limit, $offset, $where = array(), $select = '*', $order = 'id desc') {
		$topic_model = new Model_Topic();
        $rs = $topic_model->getTopicList($limit, $offset, $where, $select, $order);
        if (isset($where['class_id'])) {
			$class_model = new Model_Class();
			$rs['class'] = $class_model->get($where['class_id']);
		}
        return $rs;
    }

	public function getAllUsers($where = array(), $select = '*', $order = 'id asc') {
		$topic_model = new Model_Topic();
		$rs = $topic_model->getAllTopic($where, $select, $order);
		return $rs;
	}
}
