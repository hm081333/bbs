<?php

namespace App\Models\System;

use App\Models\BaseModel;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

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
