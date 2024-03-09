<?php

namespace App\Models\Fund;

use App\Casts\TimestampCast;
use App\Models\BaseModel;

class FundValuation extends BaseModel
{
    protected $casts = [
        'valuation_time' => TimestampCast::class,
    ];
}
