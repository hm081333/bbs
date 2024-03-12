<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use StdClass;

class OptionController extends BaseController
{
    protected function getRules()
    {
        return [
            'all' => [
            ],
            'get' => [
                'code' => ['desc' => '选项编码', 'required' => true],
            ],
        ];
    }

    /**
     * H5使用字典接口
     * @return JsonResponse
     */
    public function dict()
    {
        return Response::api('', Cache::rememberForever('option_dict', function () {
            $all = [];
            $list = $this->modelSystemSystemOption
                ->with(['items'])
                ->select(['code'])
                ->orderBy('id')
                ->get();
            foreach ($list as $item) {
                $all[$item->code] = $item->items->map(function ($option_item) {
                    return [
                        'value' => $option_item->id,
                        'label' => $option_item->value,
                    ];
                });
            }
            return $all;
        }));
    }

    /**
     * 获取所有选项配置以及选项值
     * @return JsonResponse
     */
    public function all()
    {
        // $params = $this->getParams();
        $all = [];
        $list = $this->modelSystemSystemOption
            ->with(['items'])
            ->select(['name', 'code', 'updated_at'])
            ->orderBy('id')
            ->get();
        foreach ($list as $item) {
            $all[$item->code] = $item;
            $this->modelSystemSystemOption::cacheOption($item->code, $item);
        }
        return Response::api('', $all ?: (new StdClass()));
    }

    /**
     * 获取指定选项值
     * @return JsonResponse
     * @throws Exception
     */
    public function get()
    {
        $params = $this->getParams();
        $codes = is_string($params['code']) ? explode(',', $params['code']) : $params['code'];
        $list = [];
        foreach ($codes as $code) {
            $updated_at = null;
            if (is_array($code)) {
                $updated_at = $code['time'];
                $code = $code['code'];
            }
            $list[$code] = null;
            $item = $this->modelSystemSystemOption::getCache($code);
            if (!$updated_at || $item->updated_at > $updated_at) {
                $list[$code] = $item;
            }
        }
        return Response::api('', $list ?: (new StdClass()));
    }

}
