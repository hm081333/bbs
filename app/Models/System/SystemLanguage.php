<?php

namespace App\Models\System;

use App\Models\BaseModel;

/**
 * App\Models\System\SystemLanguage
 *
 * @property int $id
 * @property string $key 标识
 * @property string $name 名称
 * @property string $locale 语言代号
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @property \Carbon\Carbon|null $deleted_at 删除时间
 * @property-read string $sex_name
 * @property-write mixed $sn
 * @property-write mixed $sort
 * @method static \Illuminate\Database\Eloquent\Builder|SystemLanguage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemLanguage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemLanguage query()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemLanguage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemLanguage whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemLanguage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemLanguage whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemLanguage whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemLanguage whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemLanguage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SystemLanguage extends BaseModel
{
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
