<?php

namespace App\Models\User;

use App\Models\BaseModel;
use App\Models\Fund\FundProduct;
use App\Utils\Tools;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserOptionalFund extends BaseModel
{
    use SoftDeletes;

    protected static function booted()
    {
        static::creating(function (UserOptionalFund $model) {
            $model->setAttribute('user_id', Tools::auth()->id('user'));
        });
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'fund_id');
    }

    public function fund(): HasOne
    {
        return $this->hasOne(FundProduct::class, 'id', 'fund_id');
    }
}
