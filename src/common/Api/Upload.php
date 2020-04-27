<?php


namespace Common\Api;

use Library\Exception\BadRequestException;
use Library\Traits\Api;
use Library\Uploader;
use function Common\DI;
use function PhalApi\T;

/**
 * 上传接口服务类
 * @ignore
 * @author LYi-Ho 2018-11-24 16:57:36
 */
class Upload extends Base
{
    use Api;

    /**
     * 接口参数规则
     * @return array
     */
    public function getRules()
    {
        $rules = parent::getRules();
        return $rules;
    }

    public function uploadImage()
    {
        $request = DI()->request->getAll();
        $image = $request['image'];
        $imageBinaryString = \Common\gzip_binary_string_decode($image['binaryString']);
        if (imagecreatefromstring($imageBinaryString) === false) {
            throw new \Library\Exception\BadRequestException(\PhalApi\T('非法文件'));
        }
        // file_put_contents(API_ROOT . '/runtime/test/'.$image['name'], $image['binaryString']);
        if (@$fp = fopen(API_ROOT . '/runtime/test/' . $image['name'], 'w+')) {
            fwrite($fp, $image['binaryString']);
            fclose($fp);
        }
        // var_dump($image);
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
        /* 返回数据 */
        $res = $up->getFileInfo();

        $return = [
            'fileName' => $res['title'] ?? '',
            'uploaded' => $res['state'] == 'SUCCESS' ? 1 : 0,
            'url' => '/api/' . $res['url'],
        ];
        exit(json_encode($return));
    }

}
