<?php

namespace App\Utils;

use App\Exceptions\Request\BadRequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use stdClass;

/**
 * 验证规则类
 */
class ValidateRule
{
    private $defaultValues = [];
    private $dataKeys = [];
    private $rules = [];
    private $messages = [];
    private $customAttributes = [];

    /**
     * 构造函数
     *
     * @param array $action_rule
     */
    public function __construct(array $action_rule)
    {
        foreach ($action_rule as $param_key => $param_rules) {
            $this->rules[$param_key] = [];
            if (!in_array($param_key, $this->dataKeys) && strpos($param_key, '*') === false) $this->dataKeys[] = $param_key;
            foreach ($param_rules as $rule_key => $rule) {
                if (is_object($rule)) {
                    // 规则为对象，表示为自定义规则
                    $this->rules[$param_key][] = $rule;
                } else if (is_int($rule_key)) {
                    // 只有规则键 表示规则为真
                    $this->$rule($param_key, true);
                } else {
                    // 有规则键 调用规则键，传入参数键与规则
                    $this->$rule_key($param_key, $rule);
                }
            }
        }
    }

    /**
     * 静态生成实例
     *
     * @param array $action_rule
     *
     * @return $this
     */
    public static function instance(array $action_rule)
    {
        return new static($action_rule);
    }

    /**
     * 列表参数验证规则
     *
     * @param $rule
     *
     * @return bool[][]|int[][]
     */
    public static function listRule($rule = [])
    {
        return array_merge([
            'limit' => ['desc' => '数量', 'required', 'min' => 1],
            'page' => ['desc' => '页码', 'required', 'min' => 1],
            'search_field' => ['desc' => '搜索字段'],
            'search_keyword' => ['desc' => '搜索关键字'],
        ], $rule);
    }

    /**
     * 检验请求
     *
     * @return array
     * @throws BadRequestException
     */
    public function validateRequest(): array
    {
        /* @var $request Request */
        $request = app(Request::class);
        $request_data = $request->all();
        // 没有参数规则的情况下，返回所有请求参数
        if (empty($this->rules)) return $request_data;
        // 有参数规则的情况下，只获取指定请求参数，并过滤null
        $request_data = $this->only(array_filter($request_data, fn($val) => isset($val)), $this->dataKeys);
        // 校验规则
        $this->validate($request_data);
        // 验证成功，替换验证成功的请求参数
        $request->merge($request_data);
        return $request_data;
    }

    public function only($data, $keys): array
    {
        $results = [];
        $placeholder = new stdClass;
        foreach ($keys as $key) {
            $value = data_get($data, $key, array_key_exists($key, $this->defaultValues) ? $this->defaultValues[$key] : $placeholder);
            if ($value !== $placeholder) Arr::set($results, $key, $value);
        }
        return $results;
    }

    /**
     * 校验
     *
     * @param $params
     *
     * @return bool
     * @throws BadRequestException
     */
    public function validate($params)
    {
        $validator = Validator::make($params, $this->rules, $this->messages, $this->customAttributes);
        if ($validator->fails()) throw new BadRequestException($validator->errors()->first());
        return true;
    }

    /**
     * 函数调用
     *
     * @param string $rule_key  验证规则
     * @param array  $arguments 函数传入参数，验证字段名|规则后续参数
     *
     * @return $this
     */
    public function __call(string $rule_key, array $arguments)
    {
        [$param_key, $rule] = $arguments;
        // 过滤常见类型的不同写法
        $rule_key = [
            'require' => 'required',
            'number' => 'numeric',
            'int' => 'integer',
        ][$rule_key] ?? $rule_key;
        switch ($rule_key) {
            case 'mobile':
                $this->rules[$param_key][] = 'max:11';
                $this->rules[$param_key][] = 'regex:/^1[0-9]{10}$/';
                break;
            case 'file':
                $this->rules[$param_key][] = new \App\Rules\FileRule;
                break;
            case 'files':
                $this->rules[$param_key][] = new \App\Rules\FilesRule;
                break;
            case 'option_item':
                $this->rules[$param_key][] = new \App\Rules\OptionItemRule($rule);
                break;
            case 'option_items':
                $this->rules[$param_key][] = new \App\Rules\OptionItemsRule($rule);
                break;
            case 'province':
                $this->rules[$param_key][] = new \App\Rules\ProvinceRule;
                break;
            case 'city':
                $this->rules[$param_key][] = new \App\Rules\CityRule;
                break;
            case 'district':
                $this->rules[$param_key][] = new \App\Rules\DistrictRule;
                break;
            case 'rules':
                // 手撸规则
                if (is_array($rule)) {
                    $this->rules[$param_key] = array_merge($this->rules[$param_key], $rule);
                } else {
                    $this->rules[$param_key] = array_merge($this->rules[$param_key], explode('|', $rule));
                }
                break;
            default:
                // 需要获取的数据key值
                if (is_bool($rule)) {
                    $rule_key_exist = array_search($rule_key, $this->rules[$param_key]);
                    // 规则为true，且不存在该规则时
                    if ($rule && $rule_key_exist === false) $this->rules[$param_key][] = $rule_key;
                    // 规则为false，且存在该规则时
                    if (!$rule && $rule_key_exist !== false) array_splice($this->rules[$param_key], $rule_key, 1);
                } else if (!is_array($rule)) {
                    $this->rules[$param_key][] = $rule_key . ':' . $rule;
                } else if (is_array($rule)) {
                    $this->rules[$param_key][] = $rule_key . ':' . implode(',', $rule);
                } else {
                    Log::error('未知的验证规则');
                    Log::error($rule_key);
                    Log::error($param_key);
                    Log::error($rule);
                    Log::error('--------------------------------------------------');
                }
                break;
        }
        return $this;
    }

    /**
     * 设置参数默认值
     *
     * @desc 用于请求没有传参时替换为默认值
     *
     * @param $param_key
     * @param $rule
     *
     * @return $this
     */
    public function default($param_key, $rule)
    {
        $this->defaultValues[$param_key] = $rule;
        return $this;
    }

    /**
     * 自定义错误信息
     *
     * @desc 替换自定义错误信息
     *
     * @param $param_key
     * @param $rule
     *
     * @return $this
     */
    public function messages($param_key, $rule)
    {
        $this->messages[$param_key] = $rule;
        return $this;
    }

    /**
     * 自定义属性值
     *
     * @desc 替换错误消息中的:attribute
     *
     * @param $param_key
     * @param $rule
     *
     * @return $this
     */
    public function desc($param_key, $rule)
    {
        $this->customAttributes[$param_key] = $rule;
        return $this;
    }

}
