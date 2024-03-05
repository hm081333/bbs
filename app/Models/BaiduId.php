<?php

namespace App\Models;

/**
 * App\Models\BaiduId
 *
 * @property int $id
 * @property \Carbon\Carbon|null $created_at 创建时间
 * @property \Carbon\Carbon|null $updated_at 更新时间
 * @property \Carbon\Carbon|null $deleted_at 删除时间
 * @property-read string $sex_name
 * @property-write mixed $sn
 * @property-write mixed $sort
 * @method static \Illuminate\Database\Eloquent\Builder|BaiduId newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BaiduId newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BaiduId query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaiduId whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BaiduId whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BaiduId whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BaiduId whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BaiduId extends BaseModel
{
}
