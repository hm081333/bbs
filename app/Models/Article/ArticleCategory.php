<?php

namespace App\Models\Article;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArticleCategory extends BaseModel
{
    protected $casts = [
        'sort' => \App\Casts\SortCast::class,
    ];
    //region 模型关联

    /**
     * 下级
     *
     * @return HasMany
     */
    public function children()
    {
        return $this->hasMany(static::class, 'pid')
            // ->where('is_show', 1)
            ->orderBy('sort')
            ->orderByDesc('id')
            ->with(['children']);
    }

    /**
     * 文章
     *
     * @return HasMany
     */
    public function articles()
    {
        return $this->hasMany(Article::class, 'category_id');
    }
    //endregion
}
