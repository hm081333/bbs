<?php

namespace App\Http\Controllers\Common;

use App\Exceptions\Request\BadRequestException;
use App\Exceptions\Server\BaseServerException;
use App\Http\Controllers\BaseController;
use App\Utils\File;
use App\Utils\Tools;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class FileController extends BaseController
{
    /**
     * 所有文件类型
     * @var string[]
     */
    private $allMimeTypes = [
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
        'potx' => 'application/vnd.openxmlformats-officedocument.presentationml.template',
        'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'sldx' => 'application/vnd.openxmlformats-officedocument.presentationml.slide',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
        'xlam' => 'application/vnd.ms-excel.addin.macroEnabled.12',
        'xlsb' => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
        'apk' => 'application/vnd.android.package-archive',
        'hqx' => 'application/mac-binhex40',
        'cpt' => 'application/mac-compactpro',
        'doc' => 'application/msword',
        'pdf' => 'application/pdf',
        'mif' => 'application/vnd.mif',
        'xls' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',
        'odc' => 'application/vnd.oasis.opendocument.chart',
        'odb' => 'application/vnd.oasis.opendocument.database',
        'odf' => 'application/vnd.oasis.opendocument.formula',
        'odg' => 'application/vnd.oasis.opendocument.graphics',
        'otg' => 'application/vnd.oasis.opendocument.graphics-template',
        'odi' => 'application/vnd.oasis.opendocument.image',
        'odp' => 'application/vnd.oasis.opendocument.presentation',
        'otp' => 'application/vnd.oasis.opendocument.presentation-template',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        'ots' => 'application/vnd.oasis.opendocument.spreadsheet-template',
        'odt' => 'application/vnd.oasis.opendocument.text',
        'odm' => 'application/vnd.oasis.opendocument.text-master',
        'ott' => 'application/vnd.oasis.opendocument.text-template',
        'oth' => 'application/vnd.oasis.opendocument.text-web',
        'sxw' => 'application/vnd.sun.xml.writer',
        'stw' => 'application/vnd.sun.xml.writer.template',
        'sxc' => 'application/vnd.sun.xml.calc',
        'stc' => 'application/vnd.sun.xml.calc.template',
        'sxd' => 'application/vnd.sun.xml.draw',
        'std' => 'application/vnd.sun.xml.draw.template',
        'sxi' => 'application/vnd.sun.xml.impress',
        'sti' => 'application/vnd.sun.xml.impress.template',
        'sxg' => 'application/vnd.sun.xml.writer.global',
        'sxm' => 'application/vnd.sun.xml.math',
        'sis' => 'application/vnd.symbian.install',
        'wbxml' => 'application/vnd.wap.wbxml',
        'wmlc' => 'application/vnd.wap.wmlc',
        'wmlsc' => 'application/vnd.wap.wmlscriptc',
        'bcpio' => 'application/x-bcpio',
        'torrent' => 'application/x-bittorrent',
        'bz2' => 'application/x-bzip2',
        'vcd' => 'application/x-cdlink',
        'pgn' => 'application/x-chess-pgn',
        'cpio' => 'application/x-cpio',
        'csh' => 'application/x-csh',
        'dvi' => 'application/x-dvi',
        'spl' => 'application/x-futuresplash',
        'gtar' => 'application/x-gtar',
        'hdf' => 'application/x-hdf',
        'jar' => 'application/java-archive',
        'jnlp' => 'application/x-java-jnlp-file',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'ksp' => 'application/x-kspread',
        'chrt' => 'application/x-kchart',
        'kil' => 'application/x-killustrator',
        'latex' => 'application/x-latex',
        'rpm' => 'application/x-rpm',
        'sh' => 'application/x-sh',
        'shar' => 'application/x-shar',
        'swf' => 'application/x-shockwave-flash',
        'sit' => 'application/x-stuffit',
        'sv4cpio' => 'application/x-sv4cpio',
        'sv4crc' => 'application/x-sv4crc',
        'tar' => 'application/x-tar',
        'tcl' => 'application/x-tcl',
        'tex' => 'application/x-tex',
        'man' => 'application/x-troff-man',
        'me' => 'application/x-troff-me',
        'ms' => 'application/x-troff-ms',
        'ustar' => 'application/x-ustar',
        'src' => 'application/x-wais-source',
        'zip' => 'application/zip',
        'pdb' => 'chemical/x-pdb',
        'xyz' => 'chemical/x-xyz',
        'ai' => 'application/postscript',
        'atom' => 'application/atom+xml',
        'bin' => 'application/octet-stream',
        'cdf' => 'application/x-netcdf',
        'class' => 'application/octet-stream',
        'dcr' => 'application/x-director',
        'dir' => 'application/x-director',
        'dll' => 'application/octet-stream',
        'dmg' => 'application/octet-stream',
        'dms' => 'application/octet-stream',
        'dtd' => 'application/xml-dtd',
        'dxr' => 'application/x-director',
        'eps' => 'application/postscript',
        'exe' => 'application/octet-stream',
        'ez' => 'application/andrew-inset',
        'gram' => 'application/srgs',
        'grxml' => 'application/srgs+xml',
        'gz' => 'application/x-gzip',
        'lha' => 'application/octet-stream',
        'lzh' => 'application/octet-stream',
        'mathml' => 'application/mathml+xml',
        'nc' => 'application/x-netcdf',
        'oda' => 'application/oda',
        'ps' => 'application/postscript',
        'rdf' => 'application/rdf+xml',
        'rm' => 'application/vnd.rn-realmedia',
        'roff' => 'application/x-troff',
        'skd' => 'application/x-koan',
        'skm' => 'application/x-koan',
        'skp' => 'application/x-koan',
        'skt' => 'application/x-koan',
        'smi' => 'application/smil',
        'smil' => 'application/smil',
        'so' => 'application/octet-stream',
        't' => 'application/x-troff',
        'texi' => 'application/x-texinfo',
        'texinfo' => 'application/x-texinfo',
        'tr' => 'application/x-troff',
        'vxml' => 'application/voicexml+xml',
        'xht' => 'application/xhtml+xml',
        'xhtml' => 'application/xhtml+xml',
        'xml' => 'application/xml',
        'xsl' => 'application/xml',
        'xslt' => 'application/xslt+xml',
        'xul' => 'application/vnd.mozilla.xul+xml',
        'iges' => 'model/iges',
        'igs' => 'model/iges',
        'mesh' => 'model/mesh',
        'msh' => 'model/mesh',
        'silo' => 'model/mesh',
        'wrl' => 'model/vrml',
        'vrml' => 'model/vrml',
        'rtf' => 'text/rtf',
        'css' => 'text/css',
        'rtx' => 'text/richtext',
        'tsv' => 'text/tab-separated-values',
        'jad' => 'text/vnd.sun.j2me.app-descriptor',
        'wml' => 'text/vnd.wap.wml',
        'wmls' => 'text/vnd.wap.wmlscript',
        'etx' => 'text/x-setext',
        'asc' => 'text/plain',
        'htm' => 'text/html',
        'html' => 'text/html',
        'ics' => 'text/calendar',
        'ifb' => 'text/calendar',
        'sgm' => 'text/sgml',
        'sgml' => 'text/sgml',
        'txt' => 'text/plain',
        'ice' => 'x-conference/x-cooltalk',
    ];

    /**
     * 文件类型
     * @var array[]
     */
    private $mimeTypes = [
        'image' => [
            'bmp' => 'image/bmp',
            'gif' => 'image/gif',
            //'ief' => 'image/ief',
            'png' => 'image/png',
            //'wbmp' => 'image/vnd.wap.wbmp',
            //'ras' => 'image/x-cmu-raster',
            //'pnm' => 'image/x-portable-anymap',
            //'pbm' => 'image/x-portable-bitmap',
            //'pgm' => 'image/x-portable-graymap',
            //'ppm' => 'image/x-portable-pixmap',
            //'rgb' => 'image/x-rgb',
            //'xbm' => 'image/x-xbitmap',
            //'xpm' => 'image/x-xpixmap',
            //'xwd' => 'image/x-xwindowdump',
            //'cgm' => 'image/cgm',
            //'djv' => 'image/vnd.djvu',
            //'djvu' => 'image/vnd.djvu',
            //'ico' => 'image/x-icon',
            //'jp2' => 'image/jp2',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            //'mac' => 'image/x-macpaint',
            //'pct' => 'image/pict',
            //'pic' => 'image/pict',
            //'pict' => 'image/pict',
            //'pnt' => 'image/x-macpaint',
            //'pntg' => 'image/x-macpaint',
            //'qti' => 'image/x-quicktime',
            //'qtif' => 'image/x-quicktime',
            //'svg' => 'image/svg+xml',
            //'tif' => 'image/tiff',
            //'tiff' => 'image/tiff',
            //'webp' => 'image/webp',
        ],
        'video' => [
            //'mxu' => 'video/vnd.mpegurl',
            'flv' => 'video/x-flv',
            //'wm' => 'video/x-ms-wm',
            //'wmv' => 'video/x-ms-wmv',
            //'wmx' => 'video/x-ms-wmx',
            //'wvx' => 'video/x-ms-wvx',
            'avi' => 'video/x-msvideo',
            //'movie' => 'video/x-sgi-movie',
            //'3gp' => 'video/3gpp',
            //'dif' => 'video/x-dv',
            //'dv' => 'video/x-dv',
            //'m4u' => 'video/vnd.mpegurl',
            //'m4v' => 'video/x-m4v',
            'mov' => 'video/quicktime',
            'mp4' => 'video/mp4',
            'mpe' => 'video/mpeg',
            'mpeg' => 'video/mpeg',
            'mpg' => 'video/mpeg',
            //'ogv' => 'video/ogv',
            //'qt' => 'video/quicktime',
            //'webm' => 'video/webm',
        ],
        'audio' => [
            'ogg' => 'audio/ogg',
            'm3u' => 'audio/x-mpegurl',
            'ra' => 'audio/x-pn-realaudio',
            'wav' => 'audio/x-wav',
            'wma' => 'audio/x-ms-wma',
            'wax' => 'audio/x-ms-wax',
            'aif' => 'audio/x-aiff',
            'aifc' => 'audio/x-aiff',
            'aiff' => 'audio/x-aiff',
            'au' => 'audio/basic',
            'kar' => 'audio/midi',
            'm4a' => 'audio/mp4a-latm',
            'm4p' => 'audio/mp4a-latm',
            'mid' => 'audio/midi',
            'midi' => 'audio/midi',
            'mp2' => 'audio/mpeg',
            'mp3' => 'audio/mpeg',
            'mpga' => 'audio/mpeg',
            'ram' => 'audio/x-pn-realaudio',
            'snd' => 'audio/basic',
        ],
    ];

    /**
     * 文件大小限制
     * @var float[]|int[]
     */
    private $sizes = [
        'image' => 10 * 1024 * 1024,
        'video' => 10 * 1024 * 1024,
        'audio' => 10 * 1024 * 1024,
    ];

    protected function getRules()
    {
        return [
            'uploadImage' => [
                'path' => ['desc' => '保存路径', 'string', 'required'],
            ],
            'remoteImage' => [
                'url' => ['desc' => '远程文件链接', 'string', 'url', 'required'],
                'path' => ['desc' => '保存路径', 'string', 'required'],
            ],
            'uploadVideo' => [
                'path' => ['desc' => '保存路径', 'string', 'required'],
            ],
        ];
    }

    /**
     * 上传图片
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws BadRequestException
     * @throws BaseServerException
     */
    public function uploadImage(Request $request)
    {
        $params = $this->getParams();
        $returnData = [];
        if ($file = $request->file('file')) {
            if (is_array($file)) {
                foreach ($file as $item) {
                    try {
                        $returnData[] = $this->_uploadImage($item, $params['path']);
                    } catch (\Exception $e) {
                        $returnData[] = null;
                    }
                }
            } else {
                $returnData = $this->_uploadImage($file, $params['path']);
            }
        }
        return $this->success('上传成功', $returnData);
    }

    /**
     * 上传图片逻辑
     *
     * @param UploadedFile $file
     * @param string $path
     *
     * @return array
     * @throws BadRequestException
     * @throws BaseServerException
     */
    private function _uploadImage(UploadedFile $file, string $path)
    {
        if (!$file->isValid()) throw new BadRequestException('文件上传失败');
        //检查是否不允许的文件格式
        if (!in_array($file->getClientMimeType(), $this->mimeTypes['image']) || !array_key_exists(strtolower($file->getClientOriginalExtension()), $this->mimeTypes['image'])) throw new BadRequestException('不允许上传的文件类型');
        //检查文件大小是否超出限制
        if ($file->getSize() >= $this->sizes['image']) throw new BadRequestException('文件大小超出网站限制');
        // 保存文件并返回id与文件链接
        $modelFile = Tools::file()->setUploadedFile($file)->save($path);
        return [
            'id' => $modelFile->id,
            'path' => $modelFile->path,
            //// 初次返回时使用本地链接，防止队列延迟
            //'url' => Tools::url('storage/' . $modelFile->path),
            'url' => $modelFile->url,
        ];
    }

    /**
     * 远程图片
     *
     * @return JsonResponse
     * @throws BadRequestException
     * @throws BaseServerException
     */
    public function remoteImage()
    {
        $params = $this->getParams();
        // 保存文件并返回id与文件链接
        $modelFile = Tools::file()->setRemoteFile($params['url'])->save($params['path']);
        return $this->success('上传成功', [
            'id' => $modelFile->id,
            'path' => $modelFile->path,
            //// 初次返回时使用本地链接，防止队列延迟
            //'url' => Tools::url('storage/' . $modelFile->path),
            'url' => $modelFile->url,
        ]);
    }

    /**
     * 上传视频
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws BadRequestException
     * @throws BaseServerException
     */
    public function uploadVideo(Request $request)
    {
        $params = $this->getParams();
        $returnData = [];
        if ($file = $request->file('file')) {
            if (is_array($file)) {
                foreach ($file as $item) {
                    try {
                        $returnData[] = $this->_uploadVideo($item, $params['path']);
                    } catch (\Exception $e) {
                        $returnData[] = null;
                    }
                }
            } else {
                $returnData = $this->_uploadVideo($file, $params['path']);
            }
        }
        return $this->success('上传成功', $returnData);
    }

    /**
     * 上传视频逻辑
     *
     * @param UploadedFile $file
     * @param string $path
     *
     * @return array
     * @throws BadRequestException
     * @throws BaseServerException
     */
    private function _uploadVideo(UploadedFile $file, string $path)
    {
        if (!$file->isValid()) throw new BadRequestException('文件上传失败');
        //检查是否不允许的文件格式
        if (!in_array($file->getClientMimeType(), $this->mimeTypes['video']) || !array_key_exists(strtolower($file->getClientOriginalExtension()), $this->mimeTypes['video'])) throw new BadRequestException('不允许上传的文件类型');
        //检查文件大小是否超出限制
        if ($file->getSize() >= $this->sizes['video']) throw new BadRequestException('文件大小超出网站限制');
        $file_info = (new \getID3)->analyze($file);
        $duration = $file_info['playtime_seconds'];
        unset($file_info);
        // 保存文件并返回id与文件链接
        $modelFile = Tools::file()->setUploadedFile($file)->save($path);
        return [
            'id' => $modelFile->id,
            'path' => $modelFile->path,
            //// 初次返回时使用本地链接，防止队列延迟
            //'url' => Tools::url('storage/' . $modelFile->path),
            'url' => $modelFile->url,
            // 时长
            'duration' => $duration,
        ];
    }

}
