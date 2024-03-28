<?php

namespace App\Http\Controllers\Fund;

use App\Exceptions\Server\InternalServerErrorException;
use App\Http\Controllers\BaseController;
use App\Utils\Tools;
use App\Utils\ValidateRule;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;
use Psr\SimpleCache\InvalidArgumentException;

class ConfigController extends BaseController
{
    public function getRules()
    {
        return [
        ];
    }

    /**
     * 配置信息
     *
     * @return JsonResponse
     * @throws InternalServerErrorException
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     */
    public function index()
    {
        $config = [
            // 最后开门日期
            'last_open_day' => Tools::lastOpenDoorDay()->format('Y-m-d'),
            // 最后净值结算日期
            'last_net_value_day' => null,
        ];
        $last_net_value_time = Tools::model()->FundFundProduct->orderByDesc('net_value_time')->value('net_value_time');
        if ($last_net_value_time) {
            $config['last_net_value_day'] = $last_net_value_time->format('Y-m-d');
        }
        return Response::api('', $config);
    }

}
