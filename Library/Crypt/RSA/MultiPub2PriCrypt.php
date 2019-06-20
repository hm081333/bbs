<?php

namespace Library\Crypt\RSA;

/**
 * MultiPub2PriCrypt 超长RSA加密
 *
 * RSA - 公钥加密，私钥解密 - 超长字符串的应对方案
 *
 * @package     Crypt\RSA
 */
class MultiPub2PriCrypt extends \Library\Crypt\RSA\MultiBase
{

    protected $pub2pri;

    public function __construct($config)
    {
        $this->pub2pri = new \Library\Crypt\RSA\Pub2PriCrypt($config);

        parent::__construct();
    }

    protected function doEncrypt($toCryptPie, $publicKey = null)
    {
        return $this->pub2pri->encrypt($toCryptPie, $publicKey);
    }

    protected function doDecrypt($encryptPie, $privateKey = null)
    {
        return $this->pub2pri->decrypt($encryptPie, $privateKey);
    }
}
