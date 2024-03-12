<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class SystemController extends BaseController
{
    protected function getRules()
    {
        return [
        ];
    }

    public function config(string $type = 'more')
    {
        return Response::api('', $this->modelSystemSystemConfig::getList($type));
    }

}
