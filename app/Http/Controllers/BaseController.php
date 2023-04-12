<?php

namespace App\Http\Controllers;

use App\Exceptions\Request\BadRequestException;
use App\Traits\Auth\UserAuth;
use App\Traits\JsonResponses;
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
 * @property \App\Models\AuthModel $modelAuthModel AuthModel
 * @property \App\Models\BaiduId $modelBaiduId BaiduId
 * @property \App\Models\BaseModel $modelBaseModel BaseModel
 * @property \App\Models\User $modelUser User
 * Class Controller
 */
class BaseController extends \Illuminate\Routing\Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use JsonResponses;
    use UserAuth;

    public function __get($name)
    {
        if (strpos($name, 'model') !== false) {
            $model_class = config("model_map.{$name}");
            if (class_exists($model_class)) {
                $this->$name = new $model_class;
                return $this->$name;
            }
        }
        throw new Exception('非法调用不存在函数');
    }

    /**
     * 获取在身份验证期间要使用的守卫。
     * @return JWTGuard|Guard
     */
    public function guard($name = null)
    {
        return auth($name);
    }

    /**
     * 获取参数函数
     * @param bool|array $rule
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
     * 接口参数规则定义
     * @return array
     */
    protected function getRules()
    {
        return [];
    }

}
