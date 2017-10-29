<?php

class Domain_Reply {

    public function getReplyList($where = array(), $select = '*', $order = 'reply_id desc') {
        $reply_model = new Model_Reply();
        $rs = $reply_model->getReplyList($where, $select, $order);
        return $rs;
    }

	public function add_Reply($topic_id, $user_id, $reply_detail) {
		$pic_path = '';
		if (!empty($_FILES['reply_pics']['tmp_name'])) {
			$reback = DI()->tool->upLoadImage('reply_pics');
			if (is_array($reback)) {
				$pic_path = $reback['url'];
			} else {
				throw new PhalApi_Exception_InternalServerError(T('图片上传失败'), 2);// 抛出服务端错误
			}
		}

		$user_model = new Model_User();
		$user = $user_model->get($user_id);
		$reply_model = new Model_Reply();
		$reply_id = $reply_model->ReplyCount(array('topic_id' => $topic_id)) + 1;
		$reply_data = array();
		$reply_data['topic_id'] = $topic_id;
		$reply_data['reply_id'] = $reply_id;
		$reply_data['user_id'] = $user['id'];
		$reply_data['reply_name'] = $user['user_name'];
		$reply_data['reply_email'] = $user['email'];
		$reply_data['reply_detail'] = $reply_detail;
		$reply_data['reply_pics'] = $pic_path;
		$reply_data['add_time'] = NOW_TIME;
		$rs = $reply_model->insert($reply_data);
		return $rs;
	}
}
