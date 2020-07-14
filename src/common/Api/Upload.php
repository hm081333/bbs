<?php


namespace Common\Api;

use Library\Exception\BadRequestException;
use Library\Uploader;
use function Common\createDir;
use function Common\DI;
use function Common\res_path;
use function Common\server_path;
use function PhalApi\T;

/**
 * 上传接口服务类
 * @ignore
 * @author LYi-Ho 2018-11-24 16:57:36
 */
class Upload extends Base
{
    /**
     * 接口参数规则
     * @return array
     */
    public function getRules()
    {
        $rules = parent::getRules();
        $rules['uploadImageWithBinaryString'] = [
            'path' => ['name' => 'path', 'type' => 'string', 'default' => 'images', 'desc' => '保存路径'],
            'image' => ['name' => 'image', 'type' => 'string', 'default' => [], 'desc' => '上传的图片信息'],
        ];
        return $rules;
    }

    /**
     * 上传二进制图片
     * @return array
     * @throws BadRequestException
     */
    public function uploadImageWithBinaryString()
    {
        $image = $this->image;
        if (empty($image)) throw new BadRequestException(T('非法请求'));
        // $imageBinaryString = compress_binary_decode($image);
        // $imageBinaryString = compress_string_decode($image);
        $imageBinaryString = mb_convert_encoding($image, 'ISO-8859-1', 'utf-8');
        if (imagecreatefromstring($imageBinaryString) === false) {
            throw new BadRequestException(T('非法文件'));
        }
        [$width, $height, $type, $attr] = getimagesizefromstring($imageBinaryString);
        $image_name = date('YmdHis', NOW_TIME) . DI()->tool->createRandStr(4) . image_type_to_extension($type);
        $image_path = 'static/upload/' . $this->path . '/' . $image_name;
        $server_path = server_path($image_path);
        if (!is_dir(dirname($server_path))) {
            createDir(dirname($server_path));
        }
        if (@$fp = fopen($server_path, 'w+')) {
            fwrite($fp, $imageBinaryString);
            fclose($fp);
        }
        return [
            'url' => res_path($image_path),
            'src' => $image_path,
        ];
    }

    /**
     * CKFinder控件上传图片
     * @throws BadRequestException
     */
    public function CKFinder()
    {
        $config = DI()->config->get('neditor');
        /* 生成上传实例对象并完成上传 */
        $up = new Uploader('upload', [
            "pathFormat" => $config['imagePathFormat'],
            "maxSize" => $config['imageMaxSize'],
            "allowFiles" => $config['imageAllowFiles'],
        ], 'upload');
        /**
         * 得到上传文件所对应的各个参数,数组结构
         * array(
         *     "state" => "",          //上传状态，上传成功时必须返回"SUCCESS"
         *     "url" => "",            //返回的地址
         *     "title" => "",          //新文件名
         *     "original" => "",       //原始文件名
         *     "type" => ""            //文件类型
         *     "size" => "",           //文件大小
         * )
         */
        $result = $up->getFileInfo();
        // DI()->logger->debug(json_encode($up->getFileInfo()));
        if (strtolower($result['state']) != 'success') {
            throw new BadRequestException(T($result['state']));
        }

        $return = [
            'fileName' => $result['title'] ?? '',
            'uploaded' => $result['state'] == 'SUCCESS' ? 1 : 0,
            'url' => '/api/' . $result['url'],
        ];
        exit(json_encode($return));
    }

}
