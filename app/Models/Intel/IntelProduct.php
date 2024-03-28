<?php

namespace App\Models\Intel;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class IntelProduct extends BaseModel
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
     * 系列
     *
     * @return HasOne
     */
    public function series(): HasOne
    {
        return $this->hasOne(IntelProductSeries::class, 'id', 'series_id');
    }

    /**
     * 规格
     *
     * @return HasMany
     */
    public function productSpecs(): HasMany
    {
        return $this->hasMany(IntelProductSpec::class, 'product_id', 'id');
    }
    //endregion
}
