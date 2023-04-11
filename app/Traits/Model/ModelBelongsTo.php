<?php

namespace App\Traits\Model;

use App\Models\Area;
use App\Models\File;
use App\Models\OptionItem;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait ModelBelongsTo
{
    /**
     * 文件快捷关联
     * @param string $key
     * @return BelongsTo
     */
    public function belongsToFile($key)
    {
        return $this->belongsTo(File::class, $key, 'id')->select(['id', 'origin_name', 'path']);
    }

    /**
     * 选项值快捷关联
     * @param string $key
     * @return BelongsTo
     */
    public function belongsToOptionItem($key)
    {
        return $this->belongsTo(OptionItem::class, $key, 'id')->select(['id', 'value']);
    }

    /**
     * 地区快捷关联
     * @param string $key
     * @return BelongsTo
     */
    public function belongsToArea($key)
    {
        return $this->belongsTo(Area::class, $key, 'id')->select(['id', 'name']);
    }
}
