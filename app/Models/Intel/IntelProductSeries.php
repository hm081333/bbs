<?php

namespace App\Models\Intel;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class IntelProductSeries extends BaseModel
{
    use SoftDeletes;

    /**
     * 类型转换。
     *
     * @var array
     */
    protected $casts = [
    ];

    //region 模型关联

    /**
     * 分类
     *
     * @return HasOne
     */
    public function category(): HasOne
    {
        return $this->hasOne(IntelProductCategory::class, 'id', 'category_id');
    }

    /**
     * 产品
     *
     * @return HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(IntelProduct::class, 'series_id', 'id');
    }

    /**
     * 规格
     *
     * @return HasMany
     */
    public function productSpecs(): HasMany
    {
        return $this->hasMany(IntelProductSpec::class, 'series_id', 'id');
    }
    //endregion
}
