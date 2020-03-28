<?php


namespace Library;


use Library\Exception\BadRequestException;
use function PhalApi\DI;
use function PhalApi\T;

class Request extends \PhalApi\Request
{
    /**
     * 处理请求数据
     * @param array $data
     * @return array|mixed
     * @throws BadRequestException
     */
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

        $s = $data['s'] ?? '';
        $t = $data['t'] ?? '';
        $sign = $data['sign'] ?? '';
        unset($data['sign'], $data['t']);
        if (empty($sign)) {
            return [];
            throw new BadRequestException(T('签名错误'));
        } else if (empty($t) || (time() - intval($t / 1000)) > 3600) {
            // 请求构建好后不能超过1小时
            return [];
            throw new BadRequestException(T('签名错误'));
        }
        $signOriginal = $s . $t;
        $unSign = json_decode(DI()->crypt->decrypt($sign, null), true);
        if ($unSign != $signOriginal) {
            return [];
            throw new BadRequestException(T('签名错误'));
        }

        // $decryptRequest = [];
        // if (!empty($data[POST_KEY])) {
        //     // 解密请求字符串
        //     $request = json_decode(DI()->crypt->decrypt($data[POST_KEY]), true);
        //     // 请求的存活时间 1小时 = 3600秒
        //     if (!empty($request) && (time() - intval($request['t'] / 1000)) < 3600) {
        //         $decryptRequest = $request;
        //     }
        // }

        return $data;
    }
}
