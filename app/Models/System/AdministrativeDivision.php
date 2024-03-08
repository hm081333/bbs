<?php

namespace App\Models\System;

use App\Models\BaseModel;
use App\Utils\Tools;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * App\Models\System\AdministrativeDivision
 *
 * @property int $id
 * @property string $name 名称
 * @property string $attr 简称
 * @property int $code 编码
 * @property string|null $initial 首字母
 * @property int $pid 父级ID，0代表顶级，其他关联
 * @property int $level 层级，0最高
 * @property int $sort 排序
 * @property string|null $lat 中心点纬度
 * @property string|null $lng 中心点经度
 * @property \Carbon\Carbon|null $created_at 创建时间
 * @property \Carbon\Carbon|null $updated_at 更新时间
 * @property \Carbon\Carbon|null $deleted_at 删除时间
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AdministrativeDivision> $children
 * @property-read int|null $children_count
 * @property-read HasMany $label
 * @property-read string $sex_name
 * @property-read HasMany $value
 * @property-write mixed $sn
 * @method static \Illuminate\Database\Eloquent\Builder|AdministrativeDivision newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdministrativeDivision newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdministrativeDivision query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdministrativeDivision whereAttr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdministrativeDivision whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdministrativeDivision whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdministrativeDivision whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdministrativeDivision whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdministrativeDivision whereInitial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdministrativeDivision whereLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdministrativeDivision whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdministrativeDivision whereLng($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdministrativeDivision whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdministrativeDivision wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdministrativeDivision whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdministrativeDivision whereUpdatedAt($value)
 * @mixin Eloquent
 */
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
     * 获取省市数据列表
     *
     * @param int $level 多少级，省市区3级
     *
     * @return array
     */
    public function getListWithLevel(int $level = 2): array
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
     * 获取省市数据树
     *
     * @return array
     */
    public function getProvinceCityTree(): array
    {
        return Cache::rememberForever('province_city_tree', function () {
            return array_merge(Tools::translateDataToTree($this->getListWithLevel(2)));
        });
    }

    /**
     * 获取省市区数据树
     *
     * @return array
     */
    public function getProvinceCityDistrictTree(): array
    {
        return Cache::rememberForever('province_city_district_tree', function () {
            return array_merge(Tools::translateDataToTree($this->getListWithLevel(3)));
        });
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
