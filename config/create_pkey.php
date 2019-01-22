<?php
/**
 * 生成openssl证书
 * User: LYi-Ho
 * Date: 2018/11/24
 * Time: 14:41
 */

$config = ['config' => "D:\phpStudy\Apache\conf\openssl.cnf"];
$res = openssl_pkey_new($config);
openssl_pkey_export($res, $privkey, NULL, $config);
$pubkey = openssl_pkey_get_details($res);
file_put_contents('../config/bbs_private.pem', $privkey);
file_put_contents('../config/bbs_public.pem', $pubkey['key']);
