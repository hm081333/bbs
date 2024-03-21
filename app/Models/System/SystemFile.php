<?php

namespace App\Models\System;

use App\Models\BaseModel;
use App\Utils\Tools;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class SystemFile extends BaseModel
{
    //use HasFactory;
    use SoftDeletes;

    protected $hidden = [
        'path',
    ];
    protected $appends = [
        'url',
    ];

    /**
     * 获取文件URL
     * @return string
     */
    public function getUrlAttribute(): string
    {
        return Tools::storageAsset($this->getAttribute('path'));
    }

}
