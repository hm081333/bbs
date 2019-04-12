<?php

namespace Crypt\RSA;

/**
 * Pri2PubCrypt RSA原始加密
 *
 * RSA - 私钥加密，公钥解密
 *
 * @package     Crypt\RSA
 */
class Pri2PubCrypt
{
    protected $privateKey;

    protected $publicKey;

    public function __construct($config)
    {
        $this->privateKey = file_get_contents($config['privateKey']);
        $this->publicKey = file_get_contents($config['publicKey']);
    }

    public function encrypt($data, $privateKey = null)
    {
        $rs = '';
        $privateKey = $privateKey ?? $this->privateKey;
        if (@openssl_private_encrypt($data, $rs, $privateKey) === false) {
            return null;
        }

        return $rs;
    }

    public function decrypt($data, $publicKey = null)
    {
        $rs = '';
        $publicKey = $publicKey ?? $this->publicKey;
        if (@openssl_public_decrypt($data, $rs, $publicKey) === false) {
            return null;
        }

        return $rs;
    }
}
