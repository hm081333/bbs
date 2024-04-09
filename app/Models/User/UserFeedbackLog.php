<?php

namespace App\Models\User;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserFeedbackLog extends BaseModel
{
    use SoftDeletes;

    protected static function booted()
    {
        static::creating(function (self $model) {
            $model->setOperator();
        });
    }

    public function feedback()
    {
        return $this->belongsTo(UserFeedback::class);
    }
}
