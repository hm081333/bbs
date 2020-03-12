<?php


namespace Library;


use function PhalApi\DI;

class Request extends \PhalApi\Request
{
    public function genData($data)
    {
        // 兼容接收JSON的参数 @dogstar 20191228
        $postRaw = file_get_contents('php://input');
        if (!empty($postRaw)) {
            $postRawArr = json_decode($postRaw, true);
            if (!empty($postRawArr) && is_array($postRawArr)) {
                $_REQUEST = array_merge($_REQUEST, $postRawArr);
                $_POST = array_merge($_POST, $postRawArr);
            }
        }

        if (!isset($data) || !is_array($data)) {
            $data = $_REQUEST;
        }
        $decryptRequest = [];
        if (!empty($data[POST_KEY])) {
            // 解密请求字符串
            $request = json_decode(DI()->crypt->decrypt($data[POST_KEY]), true);
            // 请求的存活时间 1小时 = 3600秒
            if (!empty($request) && (time() - intval($request['t'] / 1000)) < 3600) {
                $decryptRequest = $request;
            }
        }

        return $decryptRequest;
    }
}
