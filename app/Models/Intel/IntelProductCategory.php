<?php

namespace App\Models\Intel;

use App\Models\BaseModel;

/**
 * App\Models\Intel\IntelProductCategory
 *
 * @property int $id
 * @property int $pid 上级分类ID
 * @property int $level 层级，0最高
 * @property string $panel_key 标识码
 * @property string $name 名称
 * @property string $chinese_name 中文名称
 * @property array $multilingual_name 多语言名称
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @property \Carbon\Carbon|null $deleted_at 删除时间
 * @property-read string $sex_name
 * @property-write mixed $sn
 * @property-write mixed $sort
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductCategory whereChineseName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductCategory whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductCategory whereMultilingualName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductCategory wherePanelKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductCategory wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntelProductCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class IntelProductCategory extends BaseModel
{
    /**
     * 类型转换。
     *
     * @var array
     */
    protected $casts = [
        'multilingual_name' => 'array',
    ];

}
