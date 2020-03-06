<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2018/3/5
 * Time: 11:43
 */

namespace Common\Common;

use Library\Exception\BadRequestException;
use Library\Uploader;
use function Common\DI;
use function PhalApi\T;

class NEditor
{

    /**
     * 上传操作
     * @param $action
     * @return array
     */
    public static function upload($action)
    {
        /* 上传配置 */
        $base64 = "upload";
        $config = self::getConfig();
        switch (htmlspecialchars($action)) {
            case 'image':
                $fieldName = $config['imageFieldName'];
                $config = [
                    "pathFormat" => $config['imagePathFormat'],
                    "maxSize" => $config['imageMaxSize'],
                    "allowFiles" => $config['imageAllowFiles'],
                ];
                break;
            case 'scrawl':
                $fieldName = $config['scrawlFieldName'];
                $config = [
                    "pathFormat" => $config['scrawlPathFormat'],
                    "maxSize" => $config['scrawlMaxSize'],
                    "allowFiles" => $config['scrawlAllowFiles'],
                    "oriName" => "scrawl.png",
                ];
                // $base64 = "base64";
                break;
            case 'video':
                $fieldName = $config['videoFieldName'];
                $config = [
                    "pathFormat" => $config['videoPathFormat'],
                    "maxSize" => $config['videoMaxSize'],
                    "allowFiles" => $config['videoAllowFiles'],
                ];
                break;
            case 'file':
            default:
                $fieldName = $config['fileFieldName'];
                $config = [
                    "pathFormat" => $config['filePathFormat'],
                    "maxSize" => $config['fileMaxSize'],
                    "allowFiles" => $config['fileAllowFiles'],
                ];
                break;
        }
        /* 生成上传实例对象并完成上传 */
        $up = new Uploader($fieldName, $config, $base64);
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
        return $up->getFileInfo();

    }

    /**
     * @return mixed
     */
    public static function getConfig()
    {
        return DI()->config->get('neditor');
    }

    public static function list($action)
    {
        $config = self::getConfig();
        /* 判断类型 */
        switch ($action) {
            /* 列出文件 */
            case 'listfile':
                $allowFiles = $config['fileManagerAllowFiles'];
                $listSize = $config['fileManagerListSize'];
                $path = $config['fileManagerListPath'];
                break;
            /* 列出图片 */
            case 'listimage':
            default:
                $allowFiles = $config['imageManagerAllowFiles'];
                $listSize = $config['imageManagerListSize'];
                $path = $config['imageManagerListPath'];
        }
        $allowFiles = substr(str_replace(".", "|", join("", $allowFiles)), 1);

        /* 获取参数 */
        $size = isset($_GET['size']) ? htmlspecialchars($_GET['size']) : $listSize;
        $start = isset($_GET['start']) ? htmlspecialchars($_GET['start']) : 0;
        $end = $start + $size;

        /* 获取文件列表 */
        $path = $_SERVER['DOCUMENT_ROOT'] . (substr($path, 0, 1) == "/" ? "" : "/") . $path;
        $files = self::getfiles($path, $allowFiles);
        if (!count($files)) {
            return [
                "state" => "no match file",
                "list" => [],
                "start" => $start,
                "total" => count($files),
            ];
        }

        /* 获取指定范围的列表 */
        $len = count($files);
        for ($i = min($end, $len) - 1, $list = []; $i < $len && $i >= 0 && $i >= $start; $i--) {
            $list[] = $files[$i];
        }
        //倒序
        //for ($i = $end, $list = array(); $i < $len && $i < $end; $i++){
        //    $list[] = $files[$i];
        //}

        /* 返回数据 */
        $result = [
            "state" => "SUCCESS",
            "list" => $list,
            "start" => $start,
            "total" => count($files),
        ];

        return $result;

    }

    /**
     * 遍历获取目录下的指定类型的文件
     * @param       $path
     * @param array $files
     * @return array
     */
    public static function getfiles($path, $allowFiles, &$files = [])
    {
        if (!is_dir($path)) return null;
        if (substr($path, strlen($path) - 1) != '/') $path .= '/';
        $handle = opendir($path);
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                $path2 = $path . $file;
                if (is_dir($path2)) {
                    self::getfiles($path2, $allowFiles, $files);
                } else {
                    if (preg_match("/\.(" . $allowFiles . ")$/i", $file)) {
                        $files[] = [
                            'url' => substr($path2, strlen($_SERVER['DOCUMENT_ROOT'])),
                            'mtime' => filemtime($path2),
                        ];
                    }
                }
            }
        }
        return $files;
    }

    public static function crawler()
    {
        set_time_limit(0);
        $config = self::getConfig();
        /* 上传配置 */
        $fieldName = $config['catcherFieldName'];
        $config = [
            "pathFormat" => $config['catcherPathFormat'],
            "maxSize" => $config['catcherMaxSize'],
            "allowFiles" => $config['catcherAllowFiles'],
            "oriName" => "remote.png",
        ];

        /* 抓取远程图片 */
        $list = [];
        if (isset($_POST[$fieldName])) {
            $source = $_POST[$fieldName];
        } else {
            $source = $_GET[$fieldName];
        }
        foreach ($source as $imgUrl) {
            $item = new Uploader($imgUrl, $config, "remote");
            $info = $item->getFileInfo();
            array_push($list, [
                "state" => $info["state"],
                "url" => $info["url"],
                "size" => $info["size"],
                "title" => htmlspecialchars($info["title"]),
                "original" => htmlspecialchars_decode($info["original"]),
                "source" => htmlspecialchars_decode($imgUrl),
            ]);
        }

        /* 返回抓取数据 */
        return [
            'state' => count($list) ? 'SUCCESS' : 'ERROR',
            'list' => $list,
        ];
    }

}
