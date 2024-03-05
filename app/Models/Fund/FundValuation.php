<?php

namespace App\Models\Fund;

use App\Casts\Timestamp;
use App\Models\BaseModel;

/**
 * App\Models\Fund\FundValuation
 *
 * @property int $id
 * @property int $fund_id 基金ID
 * @property string $code 基金代码
 * @property string $name 基金名称
 * @property string $unit_net_value 单位净值
 * @property string $estimated_net_value 预估净值
 * @property string $estimated_growth 预估增长值
 * @property string $estimated_growth_rate 预估增长率
 * @property \Carbon\Carbon $valuation_time 基金估值时间
 * @property string $valuation_source 基金估值来源
 * @property \Carbon\Carbon|null $created_at 创建时间
 * @property \Carbon\Carbon|null $updated_at 更新时间
 * @property \Carbon\Carbon|null $deleted_at 删除时间
 * @property-read string $sex_name
 * @property-write mixed $sn
 * @property-write mixed $sort
 * @method static \Illuminate\Database\Eloquent\Builder|FundValuation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FundValuation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FundValuation query()
 * @method static \Illuminate\Database\Eloquent\Builder|FundValuation whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundValuation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundValuation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundValuation whereEstimatedGrowth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundValuation whereEstimatedGrowthRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundValuation whereEstimatedNetValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundValuation whereFundId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundValuation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundValuation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundValuation whereUnitNetValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundValuation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundValuation whereValuationSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FundValuation whereValuationTime($value)
 * @mixin \Eloquent
 */
class FundValuation extends BaseModel
{
    protected $casts = [
        'valuation_time' => Timestamp::class,
    ];
}
