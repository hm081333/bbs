<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2018/11/24
 * Time: 17:35
 */

namespace Common\Model;

use PhalApi\Model\NotORMModel as NotORM;


/**
 * 文章 数据层
 * Class Topic
 * @package Common\Model
 * @author  LYi-Ho 2018-11-26 09:23:49
 */
class Topic extends NotORM
{
    use Common;

    protected function getTableName($id)
    {
        return 'topic';
    }

    /**
     * 更新文章查看数量
     * @param        $id
     * @param string $type
     * @return int
     */
    public function updateViewCount($id, $type = '+')
    {
        return $this->getORM()->where($id)->update(['view' => new \NotORM_Literal("view {$type} 1")]);// 浏览数+1
    }

    /**
     * 更新文章回复数量
     * @param        $id
     * @param string $type
     * @return int
     */
    public function updateReplyCount($id, $type = '+')
    {
        return $this->getORM()->where($id)->update(['reply' => new \NotORM_Literal("reply {$type} 1")]);// 浏览数+1
    }

}
