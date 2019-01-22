<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2018/11/24
 * Time: 17:35
 */

namespace Bbs\Model;

class Subject extends \Common\Model\Subject
{
    use \Common\Model\Common;
    
    protected function getTableName($id)
    {
        return 'class';
    }
    
}
