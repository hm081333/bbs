<?php

namespace App\Models\Forum;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumTopicType extends BaseModel
{
    use SoftDeletes;

    //region 模型关联

    /**
     * 分类对应板块
     *
     * @return BelongsTo
     */
    public function community()
    {
        return $this->belongsTo(ForumCommunity::class, 'forum_community_id');
    }

    /**
     * 分类下主题
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
