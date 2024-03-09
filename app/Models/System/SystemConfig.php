<?php

namespace App\Models\System;

use App\Models\BaseModel;
use App\Utils\Tools;
use Eloquent;
use Illuminate\Support\Collection;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * App\Models\SystemConfig
 *
 * @property int $id
 * @property string $type 系统设置类型
 * @property string $key 系统设置键
 * @property string $value 系统设置值
 * @property string|null $data_type 数据类型
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-write mixed $sort
 * @method static \Illuminate\Database\Eloquent\Builder|SystemConfig newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemConfig newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemConfig onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemConfig query()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemConfig whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemConfig whereDataType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemConfig whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemConfig whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemConfig whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemConfig whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemConfig whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemConfig whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemConfig withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemConfig withoutTrashed()
 * @mixin Eloquent
 */
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
