<?php

namespace Crypt;

/**
 * Openssl Openssl加密
 *
 * 使用openssl扩展进加解密
 *
 * <br>使用示例：<br>
 * ```
 *  $mcrypt = new \Crypt\Openssl('12345678');
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
// class OpensslCrypt implements \PhalApi\Crypt
class OpensslCrypt
{
    protected $key;
    protected $iv;
    protected $options;
    protected $method;

    public function __construct($config = null)
    {
        $this->key = $config['key'] ?? SECURITY_KEY;
        $this->iv = $config['iv'] ?? SECURITY_IV;
        $this->options = $config['options'] ?? OPENSSL_ZERO_PADDING;
        $this->method = $config['method'] ?? 'AES-256-CFB';
    }

    /**
     * 对称加密
     * @desc openssl加密
     * @param mixed  $data 等加密的数据
     * @param string $key  加密的key
     * @return mixed 加密后的数据
     */
    public function encrypt($data)
    {
        $encrypted = openssl_encrypt($data, $this->method, $this->key, $this->options, $this->iv);
        $encode = base64_encode($encrypted);
        //openssl_encrypt 加密相当于将 mcrypt_encrypt 的加密结果执行一次 base64_encode
        return $encode;
    }

    /**
     * 对称解密
     * @desc openssl解密
     * @param mixed  $data 对称加密后的内容
     * @param string $key  加密的key
     * @return mixed 解密后的数据
     */
    public function decrypt($data)
    {
        //openssl_decode 解密相当于 先将加密结果执行一次base64_decode 然后再通过mcrypt_decrypt 解密
        $encryptedData = base64_decode($data);
        $decrypted = openssl_decrypt($encryptedData, $this->method, $this->key, $this->options, $this->iv);
        //$decrypted = rtrim($decrypted, "\0");//解密出来的数据后面会出现如图所示的六个红点；这句代码可以处理掉，从而不影响进一步的数据操作
        return $decrypted;
    }

}

