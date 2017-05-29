<?php

class Domain_Class {

    public function getClassList($limit, $offset, $where = array(), $select = '*', $order = 'id') {
        $class_model = new Model_Class();
        $rs = $class_model->getClassList($limit, $offset, $where, $select, $order);
		// var_dump($rs);
        return $rs;
    }
}
