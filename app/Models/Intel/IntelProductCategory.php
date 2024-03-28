<?php

namespace App\Models\Intel;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class IntelProductCategory extends BaseModel
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
     * 下级
     *
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(static::class, 'pid', 'id');
    }

    /**
     * 系列
     *
     * @return HasMany
     */
    public function series(): HasMany
    {
        return $this->hasMany(IntelProductSeries::class, 'category_id', 'id');
    }

    /**
     * 产品
     *
     * @return HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(IntelProduct::class, 'category_id', 'id');
    }

    /**
     * 规格
     *
     * @return HasMany
     */
    public function productSpecs(): HasMany
    {
        return $this->hasMany(IntelProductSpec::class, 'category_id', 'id');
    }
    //endregion

}
