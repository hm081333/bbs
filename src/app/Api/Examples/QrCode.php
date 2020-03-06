<?php

namespace App\Api\Examples;

use PhalApi\Api;
use PhalApi\QrCode\Lite;
use function PhalApi\DI;

/**
 * 生成二维码
 */
class QrCode extends Api
{

    public function getRules()
    {
        return [
            'png' => [
                'data' => ['name' => 'data', 'require' => true, 'desc' => '待生成二维码的内容'],
                'level' => ['name' => 'level', 'type' => 'enum', 'range' => ['L', 'M', 'Q', 'H'], 'default' => 'L', 'desc' => '错误纠正级别，L为最小，H为最佳'],
                'size' => ['name' => 'size', 'type' => 'int', 'min' => 1, 'max' => 10, 'default' => 4, 'desc' => '二维码尺寸大小'],
                'isShowPic' => ['name' => 'output', 'type' => 'boolean', 'default' => true, 'desc' => '是否直接显示二维码，否的话通过base64返回二维码数据'],
            ],
        ];
    }

    /**
     * 根据文本内容，生成二维码
     * @desc 可根据传入的广西内容，生成对应的二维码，还可以调整尺寸大小。可以直接输出png图片，也可以返回base64后的图片数据。
     */
    public function png()
    {
        $qrcode = DI()->get('qrcode', new Lite());

        if ($this->isShowPic) {
            $qrcode->png($this->data, false, $this->level, $this->size);
            exit();
        } else {
            $temp = tempnam("/tmp", 'qrcode');
            $qrcode->png($this->data, $temp, $this->level, $this->size);
            return base64_encode(file_get_contents($temp));
        }
    }
}
