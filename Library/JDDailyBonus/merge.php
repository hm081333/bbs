<?php

namespace Library\JDDailyBonus;

class merge
{
    public function __set($name, $value)
    {
        // TODO: Implement __set() method.
        $this->$name = $value;
    }

    public function __get($name)
    {
        // TODO: Implement __get() method.
        if (!isset($this->$name)) {
            $this->$name = new static;
        }
        return $this->$name;
    }

}
