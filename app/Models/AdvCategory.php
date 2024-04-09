<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class AdvCategory extends BaseModel
{
    protected $casts = [
        'sort' => \App\Casts\SortCast::class,
    ];
    // protected $with = ['advs'];

    //region 模型关联
    /**
     * 广告
     *
     * @return HasMany
     */
    public function advs()
    {
        return $this->hasMany(Adv::class, 'category_id')
            ->where('is_show', 1)
            ->orderBy('sort')
            ->orderByDesc('id');
    }

    /**
     * 下级广告
     *
     * @return HasMany
     */
    public function children()
    {
        return $this->hasMany(static::class, 'pid')->with(['children']);
    }
    //endregion
}
