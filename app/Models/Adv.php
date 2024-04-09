<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Adv extends BaseModel
{
    /**
     * 应进行类型转换的属性
     *
     * @var array
     */
    protected $casts = [
        'image' => \App\Casts\FileCast::class,
        'is_show' => 'boolean',
        'sort' => \App\Casts\SortCast::class,
    ];

    //region 模型关联

    /**
     * 广告分类
     *
     * @return BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(AdvCategory::class, 'category_id');
    }

    //endregion
}
