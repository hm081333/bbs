<?php

class Domain_Reply {

    public function getReplyList($where = array(), $select = '*', $order = 'reply_id desc') {
        $reply_model = new Model_Reply();
        $rs = $reply_model->getReplyList($where, $select, $order);
        return $rs;
    }
}
