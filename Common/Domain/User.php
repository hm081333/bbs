<?php

class Domain_User {
    public function getAllUsers() {
        $user_model = new Model_User();
        $rs = $user_model->getAllUsers();
		// var_dump($rs);
        return $rs;
    }

	public function userInfo($user_id) {
		$user_model = new Model_User();
		$rs['info'] = $user_model->get($user_id);
		$topic_model = new Model_Topic();
		$rs['topic_count'] = $topic_model->TopicCount(array('user_id' => $user_id));
		$reply_model = new Model_Reply();
		$rs['reply_count'] = $reply_model->ReplyCount(array('user_id' => $user_id));
		return $rs;
	}

	public function edit_Member($user_id, $password, $email, $realname) {
		$user_model = new Model_User();
		$update_data = array();
		$update_data['email'] = $email;
		$update_data['realname'] = $realname;
		if (!empty($password)) {
			$update_data['password'] = password_hash($password, PASSWORD_BCRYPT);
		}
		$rs = $user_model->update($user_id, $update_data);
		if ($rs === false) {
			throw new PhalApi_Exception_InternalServerError(T('修改失败'), 2);// 抛出服务端错误
		} else {
			return true;
		}

	}


}
