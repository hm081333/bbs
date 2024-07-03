<?php

namespace App\Models\User;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDailySignIn extends BaseModel
{
    use SoftDeletes;
}
