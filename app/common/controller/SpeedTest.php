<?php
declare (strict_types=1);

namespace app\common\controller;

use think\Request;

class SpeedTest
{
    public function empty()
    {
        $header = [];

        // if (\request()->get('cors') !== null) {
        //     $header['Access-Control-Allow-Origin'] = '*';
        //     $header['Access-Control-Allow-Methods'] = 'GET, POST';
        //     $header['Access-Control-Allow-Headers'] = 'Content-Encoding, Content-Type';
        // }

        $header['Cache-Control'] = 'store, no-cache, must-revalidate, max-age=0, s-maxage=0, post-check=0, pre-check=0';
        // $header['Cache-Control'] = 'post-check=0, pre-check=0';
        $header['Pragma'] = 'no-cache';
        $header['Connection'] = 'keep-alive';
        return response()->header($header);
    }

    /**
     * @return int
     */
    private function getChunkCount()
    {
        if (
            !array_key_exists('ckSize', \request()->get())
            || !ctype_digit(\request()->get('ckSize'))
            || (int)\request()->get('ckSize') <= 0
        ) {
            return 4;
        }

        if ((int)\request()->get('ckSize') > 1024) {
            return 1024;
        }

        return (int)\request()->get('ckSize');
    }

    /**
     * @return string[]
     */
    private function sendHeaders()
    {
        $header = [];

        // if (\request()->get('cors') !== null) {
        //     $header['Access-Control-Allow-Origin'] = '*';
        //     $header['Access-Control-Allow-Methods'] = 'GET, POST';
        // }

        // Indicate a file download
        $header['Content-Description'] = 'File Transfer';
        $header['Content-Type'] = 'application/octet-stream';
        $header['Content-Disposition'] = 'attachment; filename=random.dat';
        $header['Content-Transfer-Encoding'] = 'binary';

        // Cache settings: never cache this request
        $header['Cache-Control'] = 'no-store, no-cache, must-revalidate, max-age=0, s-maxage=0, post-check=0, pre-check=0';
        $header['Pragma'] = 'no-cache';
        return $header;
    }

    public function garbage()
    {
        // Disable Compression
        @ini_set('zlib.output_compression', 'Off');
        @ini_set('output_buffering', 'Off');
        @ini_set('output_handler', '');
        // Determine how much data we should send
        $chunks = $this->getChunkCount();

        // Generate data
        $data = openssl_random_pseudo_bytes(1048576);

        // Deliver chunks of 1048576 bytes
        for ($i = 0; $i < $chunks; $i++) {
            echo $data;
            flush();
        }
        return response()->header($this->sendHeaders());
    }
}
