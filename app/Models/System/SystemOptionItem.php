<?php

namespace App\Models\System;

use App\Models\BaseModel;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\System\SystemOptionItem
 *
 * @property int $id
 * @property string $code 选项编码
 * @property string $value 选项值
 * @property int $sort 排序
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @property \Carbon\Carbon|null $deleted_at 删除时间
 * @property-read mixed $key
 * @property-read string $sex_name
 * @property-read \App\Models\System\SystemOption|null $option
 * @property-write mixed $sn
 * @method static \Illuminate\Database\Eloquent\Builder|SystemOptionItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemOptionItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemOptionItem onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemOptionItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemOptionItem whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemOptionItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemOptionItem whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemOptionItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemOptionItem whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemOptionItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemOptionItem whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemOptionItem withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemOptionItem withoutTrashed()
 * @mixin Eloquent
 */
class SystemOptionItem extends BaseModel
{
    //use HasFactory;
    use SoftDeletes;

    protected $touches = [
        'option',
    ];

    //region 模型关联

    /**
     * 选项名称
     * @return BelongsTo
     */
    public function option()
    {
        return $this->belongsTo(SystemOption::class, 'code', 'code');
    }
    //endregion

    //region 获取器
    /**
     * 获取器设置key，实现key-value对象
     * @return mixed
     */
    public function getKeyAttribute()
    {
        return $this->getAttribute('id');
    }
    //endregion

    //region 自定义函数
    /**
     * 获取选项值
     * @param int $id 选项值ID
     * @return string
     */
    public static function getValue(int $id): string
    {
        $value = static::getCacheOrSet($id, fn() => static::where('id', $id)->select(['id', 'value'])->value('value'));
        return empty($value) ? '' : $value;
    }
    //endregion

}
