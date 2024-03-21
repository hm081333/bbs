<?php

namespace App\Models\Fund;

use App\Casts\TimestampCast;
use App\Models\BaseModel;
use Illuminate\Support\Facades\Cache;

class FundProduct extends BaseModel
{
    protected $casts = [
        'net_value_time' => TimestampCast::class,
    ];

    public static function getList()
    {
        return static::getCacheOrSet('list', fn() => static::get()->makeHidden(['created_at', 'updated_at', 'deleted_at'])->toArray(), null);
    }

    /**
     * 根据基金代码获取基金信息
     *
     * @param string $code
     *
     * @return FundProduct|null
     */
    public static function getByCode(string $code): ?FundProduct
    {
        return static::getCacheOrSet($code, function () use ($code) {
            return static::where('code', (string)$code)->first();
        }, null);
    }
}
