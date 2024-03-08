<?php

namespace App\Http\Controllers\Common;

use App\Exceptions\Request\BadRequestException;
use App\Exceptions\Server\Exception;
use App\Http\Controllers\BaseController;
use App\Utils\File;
use App\Utils\Tools;
use EasyWeChat\Pay\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class NotifyController extends BaseController
{
    protected function getRules()
    {
        return [
        ];
    }

}
