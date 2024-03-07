<?php

namespace App\Models\System;

use App\Models\BaseModel;
use App\Utils\Tools;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\File
 *
 * @property int $id
 * @property string $name 文件名
 * @property string $path 文件路径
 * @property string $origin_name 原始文件名
 * @property string $mime_type 文件类型
 * @property string $extension 文件后缀
 * @property string|null $size 文件大小
 * @property string|null $width 宽度
 * @property string|null $height 高度
 * @property \Carbon\Carbon|null $created_at 创建时间
 * @property \Carbon\Carbon|null $updated_at 更新时间
 * @property \Carbon\Carbon|null $deleted_at 删除时间
 * @property-read string $sex_name
 * @property-read string $url
 * @property-write mixed $sn
 * @property-write mixed $sort
 * @method static Builder|SystemFile newModelQuery()
 * @method static Builder|SystemFile newQuery()
 * @method static Builder|SystemFile onlyTrashed()
 * @method static Builder|SystemFile query()
 * @method static Builder|SystemFile whereCreatedAt($value)
 * @method static Builder|SystemFile whereDeletedAt($value)
 * @method static Builder|SystemFile whereExtension($value)
 * @method static Builder|SystemFile whereHeight($value)
 * @method static Builder|SystemFile whereId($value)
 * @method static Builder|SystemFile whereMimeType($value)
 * @method static Builder|SystemFile whereName($value)
 * @method static Builder|SystemFile whereOriginName($value)
 * @method static Builder|SystemFile wherePath($value)
 * @method static Builder|SystemFile whereSize($value)
 * @method static Builder|SystemFile whereUpdatedAt($value)
 * @method static Builder|SystemFile whereWidth($value)
 * @method static Builder|SystemFile withTrashed()
 * @method static Builder|SystemFile withoutTrashed()
 * @mixin Eloquent
 */
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
