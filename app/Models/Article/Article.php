<?php

namespace App\Models\Article;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends BaseModel
{
    /**
     * 应进行类型转换的属性
     *
     * @var array
     */
    protected $casts = [
        'cover' => \App\Casts\FileCast::class,
        'sort' => \App\Casts\SortCast::class,
    ];

    //region 模型关联

    /**
     * 分类
     *
     * @return BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(ArticleCategory::class, 'category_id');
    }
    //endregion
}
