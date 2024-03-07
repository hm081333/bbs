<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Http\JsonResponse;

class SystemController extends BaseController
{
    protected function getRules()
    {
        return [
        ];
    }

    public function config(string $type = 'more')
    {
        return $this->success('', $this->modelSystemConfig::getList($type));
    }

}
