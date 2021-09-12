<?php

namespace library;

/**
 * 序列化 类
 * Class Serialize
 * @author LYi-Ho 2019-06-19 16:43:10
 */
class Serialize
{
    protected $igbinary = false;// 是否使用 igbinary

    /**
     * Serialize constructor.
     */
    public function __construct()
    {
        if (function_exists('igbinary_serialize') && function_exists('igbinary_unserialize')) {
            $this->igbinary = true;
        }
    }

    /**
     * 序列化
     * @param mixed $data
     * @param bool $igbinary
     * @return string
     */
    public function encrypt($data, $igbinary = true)
    {
        if ($this->igbinary && $igbinary) {
            return igbinary_serialize($data);
        } else {
            return serialize($data);
        }
    }

    /**
     * 反序列化
     * @param mixed $data
     * @param bool $igbinary
     * @return mixed
     */
    public function decrypt($data, $igbinary = true)
    {
        if ($this->igbinary && $igbinary) {
            return igbinary_unserialize($data);
        } else {
            return unserialize($data);
        }
    }
}
