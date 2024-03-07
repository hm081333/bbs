<?php

namespace App\Models\System;

use App\Models\System\AdministrativeDivision;
use App\Models\BaseModel;
use App\Utils\Tools;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class AdministrativeDivision extends BaseModel
{
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    //region 模型关联

    /**
     * 下级
     * @return HasMany
     */
    public function children()
    {
        return $this->hasMany(static::class, 'pid', 'id');
    }
    //endregion

    //region 获取器
    /**
     * 数值
     * @return HasMany
     */
    public function getValueAttribute()
    {
        return $this->getAttribute('id');
    }

    /**
     * 标签
     * @return HasMany
     */
    public function getLabelAttribute()
    {
        return $this->getAttribute('name');
    }
    //endregion

    //region 自定义函数
    /**
     * 获取省市数据列表
     * @return array
     */
    public function getProvinceCityList(): array
    {
        return Cache::rememberForever('province_city_list', function () {
            return $this
                ->select([
                    'id',
                    'pid',
                    //'code',
                    'name',
                    'attr',
                    //'initial',
                    //'level',
                ])
                ->where('level', '<', 2)
                ->get()
                ->toArray();
        });
    }

    /**
     * 获取省市数据树
     * @return array
     */
    public function getProvinceCityTree(): array
    {
        return Cache::rememberForever('province_city_tree', function () {
            return array_merge(Tools::translateDataToTree($this->getProvinceCityList()));
        });
    }

    /**
     * 获取城市数据
     *
     * @param int $id 城市ID
     * @param string $column 获取指定字段
     *
     * @return AdministrativeDivision|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|mixed|object|string|null
     */
    public static function getValue(int $id, string $column = '')
    {
        $cache_key = static::getCacheKey($id);
        $area = Cache::get($cache_key);
        if (empty($area)) {
            $area = static::where('id', $id)->first();
            if (!empty($area)) {
                Cache::forever($cache_key, $area);
            }
        }
        return empty($column) ? $area : $area[$column] ?? '';
    }
    //endregion
}
