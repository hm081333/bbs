<?php

namespace App\Http\Controllers\Common;

use App\Exceptions\Request\BadRequestException;
use App\Http\Controllers\BaseController;
use App\Utils\Tools;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

class AdministrativeDivisionController extends BaseController
{
    protected function getRules()
    {
        return [
            'index' => [
                'level' => ['desc' => '层级', 'int', 'min' => 1, 'max' => 3, 'default' => 3],
                'tree' => ['desc' => '返回树状结构', 'boolean', 'required', 'default' => false],
            ],
        ];
    }

    /**
     * 列表数据
     *
     * @return JsonResponse
     * @throws BadRequestException
     */
    public function index()
    {
        $params = $this->getParams();
        return Response::api('', $params['tree'] ? $this->modelSystemAdministrativeDivision->getTreeWithLevel($params['level']) : $this->modelSystemAdministrativeDivision->getListWithLevel($params['level']));
    }

    /**
     * 省市联动
     *
     * @return JsonResponse
     */
    public function province_city_list()
    {
        return Response::api('', $this->modelSystemAdministrativeDivision->getListWithLevel(2));
    }

    /**
     * 省市联动
     *
     * @return JsonResponse
     */
    public function province_city_tree()
    {
        return Response::api('', $this->modelSystemAdministrativeDivision->getTreeWithLevel(2));
    }

    /**
     * 省市区联动
     *
     * @return JsonResponse
     */
    public function province_city_district_tree()
    {
        return Response::api('', $this->modelSystemAdministrativeDivision->getTreeWithLevel(3));
    }

}
