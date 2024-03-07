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
     * @return JsonResponse
     */
    public function province_city_tree()
    {
        return $this->success('', $this->modelSystemAdministrativeDivision->getProvinceCityTree());
    }

    /**
     * 列表数据
     * @return JsonResponse
     * @throws BadRequestException
     */
    public function page()
    {
        $params = $this->getParams();
        $tree = $params['tree'] ?? false;
        $list = Cache::rememberForever('province_city_page_list', function () {
            return $this->modelSystemAdministrativeDivision
                ->select([
                    'id',
                    'id AS value',
                    'pid',
                    'name',
                    'name AS label',
                ])
                ->where('level', '<', 2)
                ->get()
                ->toArray();
        });
        if ($tree) {
            $list = Cache::rememberForever('province_city_page_tree', function () use ($list) {
                return array_merge(Tools::translateDataToTree($list));
            });
        }
        return $this->success('', $list);
    }

}
