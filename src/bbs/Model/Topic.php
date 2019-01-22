<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2018/11/24
 * Time: 17:35
 */

namespace Bbs\Model;

class Topic extends \Common\Model\Topic
{
    use \Common\Model\Common;

    protected function getTableName($id)
    {
        return 'topic';
    }

}
