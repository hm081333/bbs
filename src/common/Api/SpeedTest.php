<?php


namespace Common\Api;

/**
 * 网络速度测试 接口服务类
 * @ignore
 * @author LYi-Ho 2018-11-24 16:57:36
 */
class SpeedTest extends Base
{
    /**
     * 接口参数规则
     * @return array
     */
    public function getRules()
    {
        $rules = parent::getRules();
        $rules['pingJitter'] = [
        ];
        $rules['download'] = [
            'ckSize' => ['name' => 'ckSize', 'type' => 'int', 'default' => 4, 'min' => 1, 'max' => 1024, 'desc' => '生成数据大小'],
        ];
        return $rules;
    }

    /**
     * 上传、Ping和网络抖动测试
     * @return mixed
     */
    public function pingJitter()
    {
        header("HTTP/1.1 200 OK");
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Connection: keep-alive");
        exit();
    }

    /**
     * Ping和网络抖动测试
     * @return mixed
     */
    public function download()
    {
        // 禁用压缩
        @ini_set('zlib.output_compression', 'Off');
        @ini_set('output_buffering', 'Off');
        @ini_set('output_handler', '');
        // 响应头
        header('HTTP/1.1 200 OK');
        // 下载之前...
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=random.dat');
        header('Content-Transfer-Encoding: binary');
        // 永远不缓存
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        // 生成随机数据
        $data = openssl_random_pseudo_bytes(1048576);
        // 提供1048576字节的块
        $chunks = intval($this->ckSize);
        for ($i = 0; $i < $chunks; $i++) {
            echo $data;
            flush();
        }
        return;
    }


}
