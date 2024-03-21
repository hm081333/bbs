<?php

namespace App\Models\System;

use App\Models\BaseModel;
use App\Utils\Tools;
use Eloquent;
use Illuminate\Support\Collection;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class SystemConfig extends BaseModel
{
    protected static function booted()
    {
        static::saved(function () {
            Cache::forget(static::getCacheKey());
        });
    }

    /**
     * 获取参数
     * @param $key
     * @param $default
     * @return mixed
     */
    public static function getValue($key, $default = null)
    {
        $all = static::getAll();
        return $all->where('key', $key)->value('value', $default);
    }

    /**
     * 获取所有设置
     * @return Collection
     */
    public static function getAll()
    {
        return Cache::rememberForever(static::getCacheKey(), function () {
            return static::get(['type', 'key', 'value', 'data_type'])->each(function ($val) {
                $val['value'] = match ($val['data_type']) {
                    'int', 'integer' => (int)$val['value'],
                    'array' => Tools::json_decode($val['value']),
                    default => $val['value'],
                };
            });
        });
    }

    /**
     * 获取所有设置
     * @param $type
     * @return Collection
     */
    public static function getList($type = false)
    {
        $all = static::getAll();
        if (empty($type)) return $all;
        return $all->where('type', $type)->pluck('value', 'key');
    }
}
