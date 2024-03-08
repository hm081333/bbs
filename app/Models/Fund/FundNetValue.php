<?php

namespace App\Models\Fund;

use App\Casts\Timestamp;
use App\Models\BaseModel;

class FundNetValue extends BaseModel
{
    protected $casts = [
        'net_value_time' => Timestamp::class,
    ];
}
