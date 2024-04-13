<?php

namespace App\Models\Forum;

use App\Models\BaseModel;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumReply extends BaseModel
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
     * 回复对应主题
     *
     * @return BelongsTo
     */
    public function topic()
    {
        return $this->belongsTo(ForumTopic::class, 'forum_topic_id');
    }
    //endregion
}
