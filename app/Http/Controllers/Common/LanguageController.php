<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\BaseController;
use App\Models\System\SystemLanguage;
use App\Utils\Tools;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class LanguageController extends BaseController
{
    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function index()
    {
        $locale = \App::currentLocale();
        return Response::api('', Tools::model()->SystemSystemLanguage->get(), ['current_locale' => $locale]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function list()
    {
        return Response::api('', Tools::model()->SystemSystemLanguage->get());
    }

}
