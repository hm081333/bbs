<?php


namespace Library\Tool;

use stdClass;

class ClassInstance
{
    // public $domainInstance;
    // public $modelInstance;

    public function __construct()
    {
        // $this->domainInstance = new stdClass();
        // $this->modelInstance = new stdClass();
    }

    /**
     * 读取一个对象中不存在的属性
     * @param string $name
     * @return object
     */
    public function __get($name)
    {
        if (!isset($this->$name)) {
            $this->$name = new stdClass();
        }
        return $this->$name;
    }

}
