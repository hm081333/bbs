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
    private $dataKeys = [];
    private $rules = [];
    private $messages = [];
    private $customAttributes = [];

    public function __construct($action_rule)
    {
        foreach ($action_rule as $param_key => $param_rules) {
            $this->rules[$param_key] = [];
            if (!in_array($param_key, $this->dataKeys) && strpos($param_key, '*') === false) $this->dataKeys[] = $param_key;
            foreach ($param_rules as $rule_key => $rule) {
                if (is_object($rule)) {
                    $this->rules[$param_key][] = $rule;
                } else if (is_int($rule_key)) {
                    $this->$rule($param_key, true);
                } else {
                    $this->$rule_key($param_key, $rule);
                }
            }
        }
    }

    /**
     * 列表参数验证规则
     * @param $rule
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
     * @return array
     * @throws BadRequestException
     */
    public function validateRequest()
    {
        /* @var $request Request */
        $request = app(Request::class);
        $request_data = $request->all();
        if (empty($this->rules)) return $request_data;
        $request_data = $this->only(array_filter($request_data, function ($val) {
            return isset($val);
        }), $this->dataKeys);
        $this->validate($request_data);
        return $request_data;
    }

    public function only($data, $keys)
    {
        $results = [];
        $placeholder = new stdClass;
        foreach ($keys as $key) {
            $value = data_get($data, $key, $placeholder);
            if ($value !== $placeholder) {
                Arr::set($results, $key, $value);
            }
        }
        return $results;
    }

    /**
     * 校验
     * @param $params
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
     * @param string $name 验证规则
     * @param array $arguments 函数传入参数，验证字段名|规则后续参数
     * @return $this
     */
    public function __call(string $name, array $arguments)
    {
        [$param_key, $rule] = $arguments;
        // 过滤常见类型的不同写法
        $name = [
            'require' => 'required',
            'number' => 'numeric',
            'int' => 'integer',
        ][$name] ?? $name;
        switch ($name) {
            case 'messages':
                //#TODO 自定义错误信息
                break;
            case 'mobile':
                $this->rules[$param_key][] = 'max:11';
                $this->rules[$param_key][] = 'regex:/^1[0-9]{10}$/';
                break;
            case 'file':
                $this->rules[$param_key][] = new \App\Rules\File;
                break;
            case 'files':
                $this->rules[$param_key][] = new \App\Rules\Files;
                break;
            case 'option_item':
                $this->rules[$param_key][] = new \App\Rules\OptionItem($rule);
                break;
            case 'option_items':
                $this->rules[$param_key][] = new \App\Rules\OptionItems($rule);
                break;
            case 'province':
                $this->rules[$param_key][] = new \App\Rules\Province;
                break;
            case 'city':
                $this->rules[$param_key][] = new \App\Rules\City;
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
                    $rule_key = array_search($name, $this->rules[$param_key]);
                    // 规则为true，且不存在该规则时
                    if ($rule && $rule_key === false) $this->rules[$param_key][] = $name;
                    // 规则为false，且存在该规则时
                    if (!$rule && $rule_key !== false) {
                        array_splice($this->rules[$param_key], $rule_key, 1);
                    }
                } else if (!is_array($rule)) {
                    $this->rules[$param_key][] = $name . ':' . $rule;
                } else if (is_array($rule)) {
                    $this->rules[$param_key][] = $name . ':' . implode(',', $rule);
                } else {
                    Log::error('未知的验证规则');
                    Log::error($name);
                    Log::error($param_key);
                    Log::error($rule);
                    Log::error('--------------------------------------------------');
                }
                break;
        }
        return $this;
    }

    /**
     * 自定义属性值
     * @desc 替换错误消息中的:attribute
     * @param $param_key
     * @param $rule
     * @return $this
     */
    public function desc($param_key, $rule)
    {
        $this->customAttributes[$param_key] = $rule;
        return $this;
    }

}