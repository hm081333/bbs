<?php

class Model_User extends PhalApi_Model_NotORM {

    public function getAllUsers() {
        return $this->getORM()->select('*')->fetchAll();
    }

    protected function getTableName($id) {
        return 'user';
    }
}
