<?php

namespace Crypt\RSA;

/**
 * MultiPri2PubCrypt 超长RSA加密
 *
 * RSA - 私钥加密，公钥解密 - 超长字符串的应对方案
 *
 * @package     Crypt\RSA
 */
class MultiPri2PubCrypt extends \PhalApi\Crypt\RSA\MultiBase
{

    protected $pri2pub;

    public function __construct($config)
    {
        $this->pri2pub = new \Crypt\RSA\Pri2PubCrypt($config);

        parent::__construct();
    }

    protected function doEncrypt($toCryptPie, $privateKey = null)
    {
        return $this->pri2pub->encrypt($toCryptPie, $privateKey);
    }

    protected function doDecrypt($encryptPie, $publicKey = null)
    {
        return $this->pri2pub->decrypt($encryptPie, $publicKey);
    }
}
