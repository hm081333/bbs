<?php

namespace App\Http\Controllers;

use App\Exceptions\Request\BadRequestException;
use App\Traits\Auth\UserAuth;
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
 * 模型映射类
 *
 * @property-read \App\Models\BaiduId                       modelBaiduId                      App\Models\BaiduId
 * @property-read \App\Models\Fund\Fund                     modelFundFund                     App\Models\Fund\Fund
 * @property-read \App\Models\Fund\FundNetValue             modelFundFundNetValue             App\Models\Fund\FundNetValue
 * @property-read \App\Models\Fund\FundValuation            modelFundFundValuation            App\Models\Fund\FundValuation
 * @property-read \App\Models\Intel\IntelProduct            modelIntelIntelProduct            App\Models\Intel\IntelProduct
 * @property-read \App\Models\Intel\IntelProductCategory    modelIntelIntelProductCategory    App\Models\Intel\IntelProductCategory
 * @property-read \App\Models\Intel\IntelProductSeries      modelIntelIntelProductSeries      App\Models\Intel\IntelProductSeries
 * @property-read \App\Models\Intel\IntelProductSpec        modelIntelIntelProductSpec        App\Models\Intel\IntelProductSpec
 * @property-read \App\Models\Mongodb\AccessLog             modelMongodbAccessLog             App\Models\Mongodb\AccessLog
 * @property-read \App\Models\Mongodb\SqlLog                modelMongodbSqlLog                App\Models\Mongodb\SqlLog
 * @property-read \App\Models\System\AdministrativeDivision modelSystemAdministrativeDivision App\Models\System\AdministrativeDivision
 * @property-read \App\Models\System\SystemFile             modelSystemSystemFile             App\Models\System\SystemFile
 * @property-read \App\Models\System\SystemLanguage         modelSystemSystemLanguage         App\Models\System\SystemLanguage
 * @property-read \App\Models\System\SystemOption           modelSystemSystemOption           App\Models\System\SystemOption
 * @property-read \App\Models\System\SystemOptionItem       modelSystemSystemOptionItem       App\Models\System\SystemOptionItem
 * @property-read \App\Models\User                          modelUser                         App\Models\User
 * @property-read \App\Models\UserFund                      modelUserFund                     App\Models\UserFund
 * @package App\Http\Controllers
 * @class   BaseController
 */
class BaseController extends \Illuminate\Routing\Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use UserAuth;

    public function __get($name)
    {
        if (str_starts_with($name, 'model')) {
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
     * @param array|null $rule
     *
     * @return array
     * @throws BadRequestException
     */
    protected function getParams(array|null $rule = null)
    {
        if (!is_array($rule)) {
            [$controller, $action] = explode('@', Route::currentRouteAction());
            $rules = $this->getRules() ?? [];
            $rule = $rules[$action] ?? [];
        }
        return ValidateRule::instance($rule)->validateRequest();
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
