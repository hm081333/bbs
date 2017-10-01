<?php

class Domain_User {
    public function getAllUsers($where = array(), $select = '*', $order = 'id asc') {
        $user_model = new Model_User();
        $rs = $user_model->getAllUsers($where, $select, $order);
		// var_dump($rs);
        return $rs;
    }

	public function getUserList($limit, $offset, $where = array(), $select = '*', $order = 'id asc') {
		$user_model = new Model_User();
		$rs = $user_model->getUserList($limit, $offset, $where, $select, $order);
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

	public function edit_Member($user_id, $password, $email, $real_name, $auth = false, $admin = false, $secret = false, $check = false) {
		$user_model = new Model_User();
		$update_data = array();
		$update_data['email'] = $email;
		$update_data['real_name'] = $real_name;
		if (!empty($password)) {
			$update_data['password'] = Domain_Common::hash($password);
			$update_data['e_pwd'] = DI()->tool->encrypt($password);
		}
		if ($admin && isset($auth)) {
			$update_data['auth'] = $auth;
		}
		if ($check == 1) {
			$update_data['secret'] = $secret;
		}
		$rs = $user_model->update($user_id, $update_data);
		if ($rs === false) {
			throw new PhalApi_Exception_InternalServerError(T('修改失败'), 2);// 抛出服务端错误
		} else {
			return true;
		}

	}


}
