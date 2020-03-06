<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2018/11/24
 * Time: 17:35
 */

namespace Bbs\Model;

use Library\Traits\Model;

class Topic extends \Common\Model\Topic
{
    use Model;

    protected function getTableName($id)
    {
        return 'topic';
    }

}
