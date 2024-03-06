<?php

namespace App\Models\Intel;

use App\Models\BaseModel;

/**
 * App\Models\Intel\IntelProduct
 *
 * @property int $id
 * @property string $language 规格语言
 * @property string $unique_key 唯一标识(ark_product_id:language)
 * @property int $category_id 分类ID
 * @property int $series_id 系列ID
 * @property string $ark_series_id ARK系列ID
 * @property string $ark_product_id ARK产品ID
 * @property string $name 名称
 * @property array $path 规格列表路径
 * @property array $url 规格列表URL
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @property \Carbon\Carbon|null $deleted_at 删除时间
 * @property-read string $sex_name
 * @property-write mixed $sn
 * @property-write mixed $sort
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProduct whereArkProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProduct whereArkSeriesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProduct whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProduct whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProduct whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProduct whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProduct wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProduct whereSeriesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProduct whereUniqueKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProduct whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProduct whereUrl($value)
 * @mixin \Eloquent
 */
class IntelProduct extends BaseModel
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
