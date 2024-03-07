<?php

namespace App\Models\Intel;

use App\Models\BaseModel;

/**
 * App\Models\Intel\IntelProductSpec
 *
 * @property int $id
 * @property string $language 语言
 * @property string $unique_key 唯一标识(ark_product_id:language:key)
 * @property int $category_id 分类ID
 * @property int $series_id 系列ID
 * @property int $product_id 产品ID
 * @property string $ark_series_id ARK系列ID
 * @property string $ark_product_id ARK产品ID
 * @property int $tab_index 规格分类下标
 * @property string $tab_title 规格分类名称
 * @property string $key 规格键
 * @property string $label 规格名称
 * @property string $value 规格值
 * @property string|null $value_url 规格值绑定URL
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @property \Carbon\Carbon|null $deleted_at 删除时间
 * @property-read string $sex_name
 * @property-write mixed $sn
 * @property-write mixed $sort
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductSpec newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductSpec newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductSpec query()
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductSpec whereArkProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductSpec whereArkSeriesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductSpec whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductSpec whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductSpec whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductSpec whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductSpec whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductSpec whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductSpec whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductSpec whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductSpec whereSeriesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductSpec whereTabIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductSpec whereTabTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductSpec whereUniqueKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductSpec whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductSpec whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductSpec whereValueUrl($value)
 * @mixin \Eloquent
 */
class IntelProductSpec extends BaseModel
{
}
