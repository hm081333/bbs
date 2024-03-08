<?php

namespace App\Http\Controllers\Common;

use App\Exceptions\Request\BadRequestException;
use App\Http\Controllers\BaseController;
use App\Utils\Tools;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class AdministrativeDivisionController extends BaseController
{
    protected function getRules()
    {
        return [
            'page' => [
                'tree' => ['desc' => '返回树状结构', 'boolean'],
            ],
        ];
    }

    /**
     * 省市联动
     *
     */
    public function province_city_list()
    {
        return response()->api('', $this->modelSystemAdministrativeDivision->getListWithLevel(2));
    }

    /**
     * 省市联动
     *
     * @return JsonResponse
     */
    public function province_city_tree()
    {
        return response()->api('', $this->modelSystemAdministrativeDivision->getProvinceCityTree());
    }

    /**
     * 省市区联动
     *
     * @return JsonResponse
     */
    public function province_city_district_tree()
    {
        return $this->success('', $this->modelSystemAdministrativeDivision->getProvinceCityDistrictTree());
    }

    /**
     * 列表数据
     *
     * @return JsonResponse
     * @throws BadRequestException
     */
    public function list()
    {
        $params = $this->getParams();
        $tree = $params['tree'] ?? false;
        $list = $this->modelSystemAdministrativeDivision->getListWithLevel(2);
        if ($tree) {
            $list = Cache::rememberForever('province_city_page_tree', function () use ($list) {
                return array_merge(Tools::translateDataToTree($list));
            });
        }
        return $this->success('', $list);
    }

}
