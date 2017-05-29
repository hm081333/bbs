<?php

class Domain_Topic {

    public function getTopicList($limit, $offset, $where = array(), $select = '*', $order = 'id desc') {
        $topic_model = new Model_Topic();
        $rs = $topic_model->getTopicList($limit, $offset, $where, $select, $order);
		$class_model = new Model_Class();
		$rs['class'] = $class_model->get($where['class_id']);
//		var_dump($rs);
        return $rs;
    }
}
