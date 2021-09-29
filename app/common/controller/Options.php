<?php
declare (strict_types=1);

namespace app\common\controller;

use app\BaseController;
use library\exception\BadRequestException;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Exception;
use think\Request;
use think\response\Json;

class Options extends BaseController
{

    /**
     * 获取性别数组
     * @return string|array
     */
    public function getSexName()
    {
        return success('', sexName());
    }

}
