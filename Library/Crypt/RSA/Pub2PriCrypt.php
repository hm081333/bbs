<?php

namespace Library\Crypt\RSA;

/**
 * Pub2PriCrypt 原始RSA加密
 *
 * RSA - 公钥加密，私钥解密
 *
 * @package     Crypt\RSA
 */
class Pub2PriCrypt
{
    protected $privateKey;

    protected $publicKey;

    public function __construct($config)
    {
        $this->privateKey = file_get_contents($config['privateKey']);
        $this->publicKey = file_get_contents($config['publicKey']);
    }

    public function encrypt($data, $publicKey = null)
    {
        $rs = '';
        $publicKey = $publicKey ?? $this->publicKey;
        if (@openssl_public_encrypt($data, $rs, $publicKey) === false) {
            return null;
        }

        return base64_encode($rs);
    }

    public function decrypt($data, $privateKey = null)
    {
        $rs = '';
        $privateKey = $privateKey ?? $this->privateKey;
        if (@openssl_private_decrypt(base64_decode($data), $rs, $privateKey) === false) {
            return null;
        }

        return $rs;
    }
}
