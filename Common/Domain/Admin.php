<?php

class Domain_Admin {
    public function getAllAdmins($where = array(), $select = '*', $order = 'id asc') {
        $admin_model = new Model_Admin();
        $rs = $admin_model->getAllAdmins($where, $select, $order);
		// var_dump($rs);
        return $rs;
    }

	public function getAdminList($limit, $offset, $where = array(), $select = '*', $order = 'id asc') {
		$admin_model = new Model_Admin();
		$rs = $admin_model->getAdminList($limit, $offset, $where, $select, $order);
		return $rs;
	}

	public function adminInfo($admin_id) {
		$admin_model = new Model_Admin();
		$rs['info'] = $admin_model->get($admin_id);
		$topic_model = new Model_Topic();
		$rs['topic_count'] = $topic_model->TopicCount(array('admin_id' => $admin_id));
		$reply_model = new Model_Reply();
		$rs['reply_count'] = $reply_model->ReplyCount(array('admin_id' => $admin_id));
		return $rs;
	}

	public function edit_Admin_Member($admin_id, $password, $auth) {
		$admin_model = new Model_Admin();
		$update_data = array();
		if (!empty($password)) {
			$update_data['password'] = Domain_Common::hash($password);
		}
		if (isset($auth)) {
			$update_data['auth'] = $auth;
		}
		$rs = $admin_model->update($admin_id, $update_data);
		if ($rs === false) {
			throw new PhalApi_Exception_InternalServerError(T('修改失败'), 2);// 抛出服务端错误
		} else {
			return true;
		}

	}


}
