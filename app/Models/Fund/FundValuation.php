<?php

namespace App\Models\Fund;

use App\Casts\Timestamp;
use App\Models\BaseModel;

class FundValuation extends BaseModel
{
    protected $casts = [
        'valuation_time' => Timestamp::class,
    ];
}
