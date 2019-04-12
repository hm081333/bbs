<?php

namespace Crypt\RSA;

/**
 * KeyGenerator 生成器
 *
 * RSA私钥或公钥的生成器
 *
 * @package     Crypt\RSA
 */
class KeyGenerator
{

    protected $privkey;

    protected $pubkey;

    public function __construct($config)
    {
        $res = openssl_pkey_new($config);// 生成一个新的私钥 - 生成一个新的私钥和公钥对
        openssl_pkey_export($res, $privkey, null, $config);// 将密钥导出到文件中
        $this->privkey = $privkey;

        $pubkey = openssl_pkey_get_details($res);// 获取私钥详情
        $this->pubkey = $pubkey['key'];
        openssl_pkey_free($res);// 释放私钥
    }

    public function getPriKey()
    {
        return $this->privkey;
    }

    public function getPubKey()
    {
        return $this->pubkey;
    }
}
