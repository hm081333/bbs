<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * App\Models\OptionItem
 *
 * @property int $id
 * @property string $code 选项编码
 * @property string $value 选项值
 * @property int $sort 选项排序
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read mixed $key
 * @property-read \App\Models\Option|null $option
 * @method static \Illuminate\Database\Eloquent\Builder|OptionItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OptionItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OptionItem onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OptionItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|OptionItem whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptionItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptionItem whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptionItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptionItem whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptionItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptionItem whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptionItem withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OptionItem withoutTrashed()
 * @mixin Eloquent
 */
class OptionItem extends BaseModel
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
        return $this->belongsTo(Option::class, 'code', 'code');
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
