<?php

namespace App\Models\Novel;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Novel extends BaseModel
{
    use SoftDeletes;

    public function chapters(): HasMany
    {
        return $this->hasMany(NovelChapter::class);
    }
}
