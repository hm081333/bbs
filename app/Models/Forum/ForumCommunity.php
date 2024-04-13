<?php

namespace App\Models\Forum;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumCommunity extends BaseModel
{
    use SoftDeletes;

    //region 模型关联

    /**
     * 父级板块
     *
     * @return BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(static::class, 'pid');
    }

    /**
     * 下级板块
     *
     * @return HasMany
     */
    public function children()
    {
        return $this->hasMany(static::class, 'pid')
            ->orderBy('sort')
            ->orderBy('id');
    }

    /**
     * 主题分类
     *
     * @return HasMany
     */
    public function topicTypes()
    {
        return $this->hasMany(ForumTopicType::class)
            ->where('is_show', 1)
            ->orderBy('sort')
            ->orderBy('id');
    }

    /**
     * 主题
     *
     * @return HasMany
     */
    public function topics()
    {
        return $this->hasMany(ForumTopic::class)
            ->where('is_show', 1)
            ->orderByDesc('is_top')
            ->orderBy('sort')
            ->orderByDesc('updated_at');
    }
    //endregion
}
