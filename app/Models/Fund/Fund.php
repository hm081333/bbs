<?php

namespace App\Models\Fund;

use App\Casts\Timestamp;
use App\Models\BaseModel;
use Illuminate\Support\Facades\Cache;

class Fund extends BaseModel
{
    protected $casts = [
        'net_value_time' => Timestamp::class,
    ];

    /**
     * 根据基金代码获取基金信息
     * @param string $code
     * @return Fund|null
     */
    public static function getByCode(string $code): ?Fund
    {
        return static::getCacheOrSet($code, function () use ($code) {
            return static::where('code', (string)$code)->first();
        }, null);
    }
}
