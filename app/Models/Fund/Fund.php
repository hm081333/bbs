<?php

namespace App\Models\Fund;

use App\Casts\Timestamp;
use App\Models\BaseModel;
use Illuminate\Support\Facades\Cache;

/**
 * App\Models\Fund\Fund
 *
 * @property int $id
 * @property string $code 基金代码
 * @property string $name 基金名称
 * @property string $pinyin_initial 基金名称拼音首字母
 * @property string|null $type 基金类型
 * @property string|null $unit_net_value 单位净值
 * @property string|null $cumulative_net_value 累计净值
 * @property \Illuminate\Support\Carbon|null $net_value_time 基金净值时间
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @property \Illuminate\Support\Carbon|null $deleted_at 删除时间
 * @property-read string $sex_name
 * @property-write mixed $sn
 * @property-write mixed $sort
 * @method static \Illuminate\Database\Eloquent\Builder|Fund newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Fund newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Fund query()
 * @method static \Illuminate\Database\Eloquent\Builder|Fund whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fund whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fund whereCumulativeNetValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fund whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fund whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fund whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fund whereNetValueTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fund wherePinyinInitial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fund whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fund whereUnitNetValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fund whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
        }, 3600);
    }
}
