<?php

class Domain_Class {

    public function getClassList($limit, $offset, $where = array(), $select = '*', $order = 'id asc') {
        $class_model = new Model_Class();
        $rs = $class_model->getClassList($limit, $offset, $where, $select, $order);
        return $rs;
    }

	public static function getAllClassList($where = array(), $select = '*', $order = 'id asc') {
		$class_model = new Model_Class();
		$rs = $class_model->getAllClassList($where, $select, $order);
		// var_dump($rs);
		return $rs;
	}
}
