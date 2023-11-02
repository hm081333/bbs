<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserFund
 *
 * @property int $id
 * @property int $user_id 用户ID
 * @property int $fund_id 基金ID
 * @property string $code 基金代码
 * @property string $name 基金名称
 * @property string $cost 持有成本
 * @property string $share 持有份额
 * @property string $amount 持有金额
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @property int|null $deleted_at 删除时间
 * @method static \Illuminate\Database\Eloquent\Builder|UserFund newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserFund newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserFund query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserFund whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserFund whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserFund whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserFund whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserFund whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserFund whereFundId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserFund whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserFund whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserFund whereShare($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserFund whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserFund whereUserId($value)
 * @property-read string $sex_name
 * @property-write mixed $sn
 * @property-write mixed $sort
 * @mixin \Eloquent
 */
class UserFund extends BaseModel
{
}
