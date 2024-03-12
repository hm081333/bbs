<?php

namespace App\Models\Intel;

use App\Casts\HtmlCast;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class IntelProductSpec extends BaseModel
{
    protected $casts = [
        'label_tips_rich_text' => HtmlCast::class,
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
     * @return HasOne
     */
    public function product(): HasOne
    {
        return $this->hasOne(IntelProduct::class, 'id', 'product_id');
    }

    //endregion

}
