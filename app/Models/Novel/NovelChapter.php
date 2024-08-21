<?php

namespace App\Models\Novel;

use App\Casts\HtmlCast;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class NovelChapter extends BaseModel
{
    use SoftDeletes;

    protected $casts = [
        'content' => HtmlCast::class,
    ];

    public function novel(): BelongsTo
    {
        return $this->belongsTo(Novel::class);
    }

}
