<?php
declare (strict_types=1);

namespace app\sign\controller;

use app\BaseController;
use library\exception\BadRequestException;
use library\exception\InternalServerErrorException;
use think\Request;

class BaiDuId extends BaseController
{
    public function listData()
    {
        $user = $this->request->getCurrentUser(true);
        $this->where[] = ['user_id', '=', $user['id']];
        return parent::listData();
    }
    public function allListData()
    {
        $user = $this->request->getCurrentUser(true);
        $this->where[] = ['user_id', '=', $user['id']];
        return parent::allListData();
    }

}
