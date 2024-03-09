<?php

namespace App\Models\Fund;

use App\Casts\TimestampCast;
use App\Models\BaseModel;

class FundNetValue extends BaseModel
{
    protected $casts = [
        'net_value_time' => TimestampCast::class,
    ];
}
