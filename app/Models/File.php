<?php

namespace App\Models;

use App\Jobs\ObjectStorageServiceJob;
use App\Utils\Tools;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read string $url
 * @property-write mixed $sort
 * @method static Builder|File newModelQuery()
 * @method static Builder|File newQuery()
 * @method static Builder|File onlyTrashed()
 * @method static Builder|File query()
 * @method static Builder|File whereCreatedAt($value)
 * @method static Builder|File whereDeletedAt($value)
 * @method static Builder|File whereExtension($value)
 * @method static Builder|File whereHeight($value)
 * @method static Builder|File whereId($value)
 * @method static Builder|File whereMimeType($value)
 * @method static Builder|File whereName($value)
 * @method static Builder|File whereOriginName($value)
 * @method static Builder|File wherePath($value)
 * @method static Builder|File whereSize($value)
 * @method static Builder|File whereUpdatedAt($value)
 * @method static Builder|File whereWidth($value)
 * @method static Builder|File withTrashed()
 * @method static Builder|File withoutTrashed()
 * @property-read string $sex_name
 * @property-write mixed $sn
 * @mixin Eloquent
 */
class File extends BaseModel
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
