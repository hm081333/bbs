<?php

namespace App\Http\Controllers;

use App\Exceptions\Request\BadRequestException;
use App\Traits\Auth\UserAuth;
use App\Traits\JsonResponses;
use App\Utils\Tools;
use App\Utils\ValidateRule;
use Exception;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Route;
use Tymon\JWTAuth\JWTGuard;

/**
 * 控制器类
 * @property \App\Models\BaiduId $modelBaiduId BaiduId
 * @property \App\Models\File $modelFile File
 * @property \App\Models\Fund\Fund $modelFundFund Fund
 * @property \App\Models\Fund\FundNetValue $modelFundFundNetValue FundNetValue
 * @property \App\Models\Fund\FundValuation $modelFundFundValuation FundValuation
 * @property \App\Models\Option $modelOption Option
 * @property \App\Models\OptionItem $modelOptionItem OptionItem
 * @property \App\Models\User $modelUser User
 * @property \App\Models\UserFund $modelUserFund UserFund
 * Class BaseController
 */
class BaseController extends \Illuminate\Routing\Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use JsonResponses;
    use UserAuth;

    public function __get($name)
    {
        if (str_contains($name, 'model')) {
            $name = str_replace('model', '', $name);
            return Tools::model()->$name;
        }
        throw new Exception('非法调用不存在函数');
    }

    /**
     * 接口参数规则定义
     *
     * @return array
     */
    protected function getRules()
    {
        return [];
    }

    /**
     * 获取参数函数
     *
     * @param bool|array $rule
     *
     * @return array
     * @throws BadRequestException
     */
    protected function getParams($rule = false)
    {
        if ($rule === false) {
            [$controller, $action] = explode('@', Route::currentRouteAction());
            $rules = $this->getRules() ?? [];
            $rule = $rules[$action] ?? [];
        }
        return (new ValidateRule($rule))->validateRequest();
    }

    /**
     * 获取在身份验证期间要使用的守卫。
     *
     * @return JWTGuard|Guard
     */
    public function guard($name = null)
    {
        return auth($name);
    }

}
