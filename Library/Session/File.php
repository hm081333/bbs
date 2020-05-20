<?php


namespace Library\Session;

use function Common\DI;

/**
 * SESSION 文件存储方式
 * Class File
 * @package Library\Session
 */
class File extends Basic
{
    //session-lifetime
    private $lifeTime;

    protected function cache()
    {
        // TODO: Implement cache() method.
        return new \Library\Cache\File(DI()->config->get('sys.cache.file'));
    }

}
