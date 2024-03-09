<?php

namespace App\Utils;

use App\Exceptions\Server\BaseServerException;
use App\Exceptions\Server\InternalServerErrorException;
use App\Jobs\ObjectStorageServiceJob;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class File
{
    /**
     * @var UploadedFile|\Illuminate\Http\File
     */
    private $file;
    private $hashName;
    private $originalName;
    private $mimeType;
    private $extension;
    private $size;

    /**
     * @param false|UploadedFile|string $data
     * @throws \Exception
     */
    public function __construct($data = false)
    {
        if ($data instanceof UploadedFile) {
            $this->setUploadedFile($data);
        } else if (is_string($data)) {
            if (preg_match('/^http[s]?:\/\/[^\s]+/', $data)) {
                // 远程链接
                $this->setRemoteFile($data);
            } else {
                $this->setLocalFile($data);
            }
        }
    }

    //region 设置源文件

    /**
     * 设置上传文件
     * @param UploadedFile $file
     * @return $this
     */
    public function setUploadedFile(UploadedFile $file)
    {
        $this->file = $file;
        return $this;
    }

    /**
     * 设置远程文件
     * @param string $url
     * @return $this
     * @throws InternalServerErrorException
     */
    public function setRemoteFile(string $url)
    {
        $url_info = parse_url($url);
        $file_path = Tools::curl()->getFile($url, Tools::runtimePath('remote/' . date('Y/m/d/'), true), basename($url_info['path']));
        $this->file = new \Illuminate\Http\File($file_path);
        return $this;
    }

    /**
     * 设置base64
     * @param string $base64
     * @return $this
     * @throws InternalServerErrorException
     */
    public function setBase64(string $base64)
    {
        // 正则匹配base64头
        preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64, $res);
        // 去除base64头并base64解码出文件内容
        $image_resource = base64_decode(str_replace($res[1], '', $base64));
        $temp_file_path = Tools::createDir(Tools::runtimePath('temporary/') . date('Y/m/d/'), true);
        $temp_file = $temp_file_path . uniqid() . '_' . (microtime(true) * 10000);
        $fp = fopen($temp_file, 'wb');
        if ($fp === false) throw new InternalServerErrorException('保存文件初始化失败');
        fwrite($fp, $image_resource);
        fclose($fp);
        $this->file = new \Illuminate\Http\File($temp_file);
        return $this;
    }

    /**
     * 设置本地文件
     * @param string $file_path
     * @return $this
     * @throws \Exception
     */
    public function setLocalFile(string $file_path)
    {
        $this->file = new \Illuminate\Http\File($file_path);
        return $this;
    }

    //endregion

    //region 获取文件相关信息

    /**
     * 保存文件到指定目录
     *
     * @param string $path
     *
     * @return \App\Models\System\SystemFile
     * @throws BaseServerException
     */
    public function save(string $path)
    {
        if (!$this->file) throw new BaseServerException('找不到文件');

        $file_data = [
            'name' => $this->getFileName(),
            'origin_name' => $this->getFileOriginName(),
            'mime_type' => $this->getFileMimeType(),
            'extension' => $this->getFileExtension(),
            'size' => $this->getFileSize(),
        ];
        // 对于图片，获取宽高
        $file_data = array_merge($file_data, $this->getImageSize());
        $file_data['path'] = $this->getFilePath($path);
        return \App\Models\System\SystemFile::create($file_data);
    }

    /**
     * 获取文件名（生成文件名）
     * @param $path
     * @return string
     */
    private function getFileName($path = null)
    {
        // return $this->file->hashName();
        if ($path) {
            $path = rtrim($path, '/') . '/';
        }
        $hash = $this->hashName ?: $this->hashName = Str::random(40);
        if ($extension = $this->getFileExtension()) {
            $extension = '.' . $extension;
        }
        return $path . $hash . $extension;
    }

    /**
     * 获取真实文件名
     * @return string
     */
    private function getFileOriginName()
    {
        return $this->originalName ?: $this->originalName = $this->file instanceof UploadedFile ? $this->file->getClientOriginalName() : $this->file->getFilename();
    }

    /**
     * 获取文件类型
     * @return string|null
     */
    private function getFileMimeType()
    {
        return $this->mimeType ?: $this->mimeType = $this->file instanceof UploadedFile ? $this->file->getClientMimeType() : $this->file->getMimeType();
    }

    /**
     * 获取文件扩展名
     * @return string
     */
    private function getFileExtension()
    {
        return $this->extension ?: $this->extension = $this->file instanceof UploadedFile ? $this->file->getClientOriginalExtension() : $this->file->extension();
    }

    /**
     * 获取文件大小
     * @return false|int
     */
    private function getFileSize()
    {
        return $this->size ?: $this->size = $this->file->getSize();
    }

    /**
     * 获取图片尺寸（宽高）
     * @return null[]
     */
    private function getImageSize()
    {
        $size = [
            'width' => null,
            'height' => null,
        ];
        if (strpos($this->getFileMimeType(), 'image/') !== false) {
            $image_info = getimagesize($this->file);
            [$size['width'], $size['height']] = $image_info;
        }
        return $size;
    }
    //endregion

    //region 保存

    /**
     * 获取文件路径
     * @param $path
     * @return string
     */
    private function getFilePath($path)
    {
        $save_path = $path . date('/Y/m/d');
        $file_path = $save_path . '/' . $this->getFileName();
        // move能同时兼容File与UploadFile
        $this->file->move(storage_path('app/public/') . $save_path, $this->getFileName());
        // 线上服务，尝试上传到OSS
        if (Tools::isProduction()) ObjectStorageServiceJob::dispatch($file_path, storage_path("app/public/{$file_path}"))->onQueue('oss');
        return $file_path;
    }
    //endregion


}
