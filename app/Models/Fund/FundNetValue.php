<?php

namespace App\Models\Fund;

use App\Casts\Timestamp;
use App\Models\BaseModel;

/**
 * App\Models\Fund\FundNetValue
 *
 * @property int $id
 * @property int $fund_id 基金ID
 * @property string $code 基金代码
 * @property string $name 基金名称
 * @property string $unit_net_value 单位净值
 * @property string|null $cumulative_net_value 累计净值
 * @property \Carbon\Carbon $net_value_time 基金净值时间
 * @property \Carbon\Carbon|null $created_at 创建时间
 * @property \Carbon\Carbon|null $updated_at 更新时间
 * @property \Carbon\Carbon|null $deleted_at 删除时间
 * @property-read string $sex_name
 * @property-write mixed $sn
 * @property-write mixed $sort
 * @method static \Illuminate\Database\Eloquent\Builder|FundNetValue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FundNetValue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FundNetValue query()
 * @method static \Illuminate\Database\Eloquent\Builder|FundNetValue whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundNetValue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundNetValue whereCumulativeNetValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundNetValue whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundNetValue whereFundId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundNetValue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundNetValue whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundNetValue whereNetValueTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundNetValue whereUnitNetValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundNetValue whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FundNetValue extends BaseModel
{
    protected $casts = [
        'net_value_time' => Timestamp::class,
    ];
}
