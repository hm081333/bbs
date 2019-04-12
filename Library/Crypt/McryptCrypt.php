<?php

namespace Crypt;

/**
 * Mcrypt 原始mcrypt加密
 *
 * 使用mcrypt扩展进加解密
 *
 * <br>使用示例：<br>
 * ```
 *  $mcrypt = new \Crypt\Mcrypt('12345678');
 *
 *  $data = 'I love php';
 *  $key = 'secrect';
 *
 *  // 加密
 *  $encryptData = $mcrypt->encrypt($data, $key);
 *
 *  // 解密
 *  $decryptData = $mcrypt->decrypt($encryptData, $key);
 * ```
 *
 * @package     Crypt
 * @link        http://php.net/manual/zh/function.mcrypt-generic.php
 */
// class McryptCrypt implements \PhalApi\Crypt
class McryptCrypt
{
    protected $key;
    protected $iv;
    protected $cipher;
    protected $mode;

    public function __construct($config = null)
    {
        $this->key = $config['key'] ?? SECURITY_KEY;
        $this->iv = $config['iv'] ?? SECURITY_IV;
        $this->cipher = $config['cipher'] ?? MCRYPT_RIJNDAEL_128;
        $this->mode = $config['mode'] ?? MCRYPT_MODE_CBC;
    }

    /**
     * 对称加密
     * @desc mcrypt加密（PHP7已废弃）
     * @param mixed  $data 等加密的数据
     * @param string $key  加密的key
     * @return mixed 加密后的数据
     */
    public function encrypt($data)
    {
        $encrypted = mcrypt_encrypt($this->cipher, $this->key, $data, $this->mode, $this->iv);
        $encode = base64_encode($encrypted);
        return $encode;
    }

    /**
     * 对称解密
     * @desc mcrypt解密（PHP7已废弃）
     * @param mixed  $data 对称加密后的内容
     * @param string $key  加密的key
     * @return mixed 解密后的数据
     */
    public function decrypt($data)
    {
        $encryptedData = base64_decode($data);
        $decrypted = mcrypt_decrypt($this->cipher, $this->key, $encryptedData, $this->mode, $this->iv);
        // $decrypted = rtrim($decrypted, "\0");//解密出来的数据后面会出现如图所示的六个红点；这句代码可以处理掉，从而不影响进一步的数据操作
        return $decrypted;
    }

}

