<?php

namespace App\Utils;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

class Qiniu
{
    /**
     * @var \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    private array $config;
    private string $accessKey;
    private string $secretKey;
    private string $bucket;
    private Auth $auth;

    public function __construct()
    {
        // php composer.phar require qiniu/php-sdk
        $this->config = config('oss.configs.qiniu');
        $this->accessKey = $this->config['access_key'];
        $this->secretKey = $this->config['secret_key'];
        $this->bucket = $this->config['bucket'];
        // 初始化Auth状态
        $this->auth = new Auth($this->accessKey, $this->secretKey);
    }

    /**
     * 获取上传的凭证
     * @param string $keyToOverwrite 覆盖上传的对象名
     * @return string
     */
    private function getToken(string $keyToOverwrite = ''): string
    {
        $expires = 3600;
        $policy = null;
        $cache_key = 'qiniu-token';
        $keyToOverwrite = empty($keyToOverwrite) ? null : $keyToOverwrite;
        if (!empty($keyToOverwrite)) $cache_key .= ":{$keyToOverwrite}";
        // 使用文件缓存
        // 提前60秒过期防止token异常提前过期
        return Cache::store('file')->remember(
            $cache_key,
            $expires - 60,
            fn() => $this->auth->uploadToken($this->bucket, $keyToOverwrite ?: null, $expires, $policy, true)
        );
    }

    /**
     * 内容上传
     * @param string $object 对象保存完整路径
     * @param mixed $content 文件内容
     * @param bool $overwrite
     * @return false|void
     */
    public function putObject($object, $content, $overwrite = false)
    {
        $uploadMgr = new UploadManager();
        // 上传字符串到存储
        [$ret, $err] = $uploadMgr->put($this->getToken($overwrite ? $object : ''), $object, $content);
        if ($err !== null) {
            Log::error($err);
            return false;
        }
        // return $ret['key'];
        return $ret;
    }

    /**
     * 文件上传
     * @param string $object 对象保存完整路径
     * @param string $local_file_path 本地文件的完整路径
     * @param bool $overwrite
     * @return false|void
     * @throws \Exception
     */
    public function uploadFile($object, $local_file_path, $overwrite = false)
    {
        // 初始化 UploadManager 对象并进行文件的上传。
        $uploadMgr = new UploadManager();
        // 调用 UploadManager 的 putFile 方法进行文件的上传。
        [$ret, $err] = $uploadMgr->putFile($this->getToken($overwrite ? $object : ''), $object, $local_file_path, null, 'application/octet-stream', true, null, 'v2');
        if ($err !== null) {
            Log::error(Tools::jsonEncode($err));
            return false;
        }
        // return $ret['key'];
        return $ret;
    }

    /**
     * 获取文件信息
     * @param string $object 对象保存完整路径
     * @return false|void
     */
    public function stat($object)
    {
        $config = new \Qiniu\Config();
        $bucketManager = new \Qiniu\Storage\BucketManager($this->auth, $config);
        [$fileInfo, $err] = $bucketManager->stat($this->bucket, $object);
        if ($err !== null) {
            Log::error(Tools::jsonEncode($err));
            return false;
        }
        return $fileInfo;

    }

}
