<?php

class Domain_User {
    public function getAllUsers() {
        $user_model = new Model_User();
        $rs = $user_model->getAllUsers();
		// var_dump($rs);
        return $rs;
    }
}
