<?php

namespace App\Utils\Aliyun;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Log;
use OSS\Core\OssException;
use OSS\OssClient;
use function config;

class Oss
{
    /**
     * @var Repository|Application|mixed
     */
    private $config;

    public function __construct()
    {
        $this->config = config('oss.configs.aliyun');

        try {
            $this->ossClient = new OssClient($this->config['access_key_id'], $this->config['access_key_secret'], $this->config['endpoint']);
        } catch (OssException $e) {
            Log::error($e);
            // print $e->getMessage();
        }

    }

    /**
     * 内容上传
     * @param string $object  对象保存完整路径
     * @param mixed  $content 文件内容
     * @param array  $options 自定义信息
     * @return void
     */
    public function putObject($object, $content, $options = [])
    {
        try {
            $this->ossClient->putObject($this->config['bucket'], $object, $content, $options);
        } catch (OssException $e) {
            Log::error($e);
            // print $e->getMessage();
        }
    }

    /**
     * 文件上传
     * @param string $object          对象保存完整路径
     * @param string $local_file_path 本地文件的完整路径
     * @param array  $options         自定义信息
     * @return void
     */
    public function uploadFile($object, $local_file_path, $options = [])
    {
        try {
            $this->ossClient->uploadFile($this->config['bucket'], $object, $local_file_path, $options);
        } catch (OssException $e) {
            Log::error($e);
            // print $e->getMessage();
        }
    }

}
