<?php

namespace App\Models\User;

use App\Casts\FilesCast;
use App\Casts\HtmlCast;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserFeedback extends BaseModel
{
    use SoftDeletes;

    protected $casts = [
        'content' => HtmlCast::class,
        'images' => FilesCast::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function logs()
    {
        return $this->hasMany(UserFeedbackLog::class);
    }
}
