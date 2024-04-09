<?php

namespace App\Models\Forum;

use App\Models\BaseModel;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumTopic extends BaseModel
{
    use SoftDeletes;

    //region 模型关联

    /**
     * 发表回复的用户
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 主题对应板块
     *
     * @return BelongsTo
     */
    public function community()
    {
        return $this->belongsTo(ForumCommunity::class);
    }

    /**
     * 主题对应分类
     *
     * @return BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(ForumTopicType::class);
    }

    /**
     * 主题下回复
     *
     * @return HasMany
     */
    public function replies()
    {
        return $this->hasMany(ForumReply::class);
    }
    //endregion
}
