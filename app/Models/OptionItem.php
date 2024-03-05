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
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @property-read mixed $key
 * @property-read string $sex_name
 * @property-read \App\Models\Option|null $option
 * @property-write mixed $sn
 * @property-write mixed $sort
 * @method static \Illuminate\Database\Eloquent\Builder|OptionItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OptionItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OptionItem onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OptionItem query()
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
