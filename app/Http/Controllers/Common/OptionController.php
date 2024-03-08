<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
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
        return $this->success('', Cache::rememberForever('option_dict', function () {
            $all = [];
            $list = $this->modelOption
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
        $list = $this->modelOption
            ->with(['items'])
            ->select(['name', 'code', 'updated_at'])
            ->orderBy('id')
            ->get();
        foreach ($list as $item) {
            $all[$item->code] = $item;
            $this->modelOption::cacheOption($item->code, $item);
        }
        return $this->success('', $all ?: (new StdClass()));
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
            $item = $this->modelOption::getCache($code);
            if (!$updated_at || $item->updated_at > $updated_at) {
                $list[$code] = $item;
            }
        }
        return $this->success('', $list ?: (new StdClass()));
    }

}
