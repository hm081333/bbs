<?php
declare (strict_types=1);

namespace app\common\controller;

use app\BaseController;
use library\exception\BadRequestException;
use think\Request;

class Upload extends BaseController
{
    /**
     * 上传二进制图片
     * @param string $path  保存路径
     * @param array  $image 上传的图片信息
     * @return \think\response\Json
     */
    public function uploadImageWithBinaryString($path = 'images', $image = [])
    {
        if (empty($image)) throw new BadRequestException('非法请求');
        // $imageBinaryString = compress_binary_decode($image);
        // $imageBinaryString = compress_string_decode($image);
        $imageBinaryString = mb_convert_encoding($image, 'ISO-8859-1', 'utf-8');
        if (imagecreatefromstring($imageBinaryString) === false) {
            throw new BadRequestException('非法文件');
        }
        [$width, $height, $type, $attr] = getimagesizefromstring($imageBinaryString);
        $image_name = date('YmdHis', $this->request->time()) . createRandStr(4) . image_type_to_extension($type);
        $image_path = 'static/upload/' . $path . '/' . $image_name;
        $server_path = server_path($image_path);
        if (!is_dir(dirname($server_path))) {
            createDir(dirname($server_path));
        }
        if (@$fp = fopen($server_path, 'w+')) {
            fwrite($fp, $imageBinaryString);
            fclose($fp);
        }
        return success('', [
            'url' => res_path($image_path),
            'src' => $image_path,
        ]);
    }
}
