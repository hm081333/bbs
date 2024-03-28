<?php

namespace App\Models\System;

use App\Models\BaseModel;
use App\Utils\Tools;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class AdministrativeDivision extends BaseModel
{
    use SoftDeletes;

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    //region 模型关联

    /**
     * 下级
     *
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
     *
     * @return HasMany
     */
    public function getValueAttribute()
    {
        return $this->getAttribute('id');
    }

    /**
     * 标签
     *
     * @return HasMany
     */
    public function getLabelAttribute()
    {
        return $this->getAttribute('name');
    }
    //endregion

    //region 自定义函数
    /**
     * 获取省市区数据列表
     *
     * @param int $level 多少级，省市区3级
     *
     * @return array
     */
    public function getListWithLevel(int $level): array
    {
        return Cache::rememberForever("administrative_division_list{$level}", fn() => $this
            // ->select([
            //     'id',
            //     'pid',
            //     //'code',
            //     'name',
            //     'attr',
            //     //'initial',
            //     //'level',
            // ])
            ->where('level', '<', $level)
            ->get()
            ->toArray());
    }

    /**
     * 获取省市区数据树
     *
     * @param int $level 多少级，省市区3级
     *
     * @return array
     */
    public function getTreeWithLevel(int $level): array
    {
        return Cache::rememberForever("administrative_division_tree{$level}", fn() => array_merge(Tools::translateDataToTree($this->getListWithLevel($level))));
    }

    /**
     * 获取城市数据
     *
     * @param int    $id     城市ID
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
