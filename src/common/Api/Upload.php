<?php


namespace Common\Api;

/**
 * 上传接口服务类
 * @ignore
 * @author LYi-Ho 2018-11-24 16:57:36
 */
class Upload extends Base
{
    use Common;

    /**
     * 接口参数规则
     * @return array
     */
    public function getRules()
    {
        $rules = parent::getRules();
        return $rules;
    }

    /**
     * CKFinder控件上传图片
     * @throws \Library\Exception\BadRequestException
     */
    public function CKFinder()
    {
        $config = \Common\DI()->config->get('neditor');
        /* 生成上传实例对象并完成上传 */
        $up = new \Library\Uploader('upload', [
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
            throw new \Library\Exception\BadRequestException(\PhalApi\T($result['state']));
        }
        /* 返回数据 */
        $res = $up->getFileInfo();

        $return = [
            'fileName' => $res['title'] ?? '',
            'uploaded' => $res['state'] == 'SUCCESS' ? 1 : 0,
            'url' => '/api/' . $res['url']
        ];
        exit(json_encode($return));
    }

}
