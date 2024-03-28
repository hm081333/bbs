<?php

namespace App\Models\User;

use App\Models\BaseModel;
use App\Models\Fund\FundProduct;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserFund extends BaseModel
{
    use SoftDeletes;

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'fund_id');
    }

    public function fund(): HasOne
    {
        return $this->hasOne(FundProduct::class, 'id', 'fund_id');
    }
}
