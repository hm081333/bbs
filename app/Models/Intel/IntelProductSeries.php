<?php

namespace App\Models\Intel;

use App\Models\BaseModel;

/**
 * App\Models\Intel\IntelProductSeries
 *
 * @property int $id
 * @property string $language 规格语言
 * @property string $unique_key 唯一标识(ark_series_id:language)
 * @property int $category_id 分类ID
 * @property string $ark_series_id ARK系列ID
 * @property string $name 名称
 * @property array $path 规格列表路径
 * @property array $url 规格列表URL
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @property \Carbon\Carbon|null $deleted_at 删除时间
 * @property-read string $sex_name
 * @property-write mixed $sn
 * @property-write mixed $sort
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductSeries newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductSeries newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductSeries query()
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductSeries whereArkSeriesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductSeries whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductSeries whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductSeries whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductSeries whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductSeries whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductSeries whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductSeries wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductSeries whereUniqueKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductSeries whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductSeries whereUrl($value)
 * @mixin \Eloquent
 */
class IntelProductSeries extends BaseModel
{
    /**
     * 类型转换。
     *
     * @var array
     */
    protected $casts = [
        // 'multilingual_name' => 'array',
        // 'path' => 'array',
        // 'url' => 'array',
    ];
}
