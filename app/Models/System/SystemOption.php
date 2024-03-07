<?php

namespace App\Models\System;

use App\Models\BaseModel;
use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Cache;

/**
 * App\Models\System\SystemOption
 *
 * @property int $id
 * @property string $name 选项名称
 * @property string $code 选项编码
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @property \Carbon\Carbon|null $deleted_at 删除时间
 * @property-read string $sex_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\System\SystemOptionItem> $items
 * @property-read int|null $items_count
 * @property-write mixed $sn
 * @property-write mixed $sort
 * @method static \Illuminate\Database\Eloquent\Builder|SystemOption newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemOption newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemOption onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemOption query()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemOption whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemOption whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemOption whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemOption whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemOption whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemOption whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemOption withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemOption withoutTrashed()
 * @mixin Eloquent
 */
class SystemOption extends BaseModel
{
    //use HasFactory;
    use SoftDeletes;

    // protected $with = ['items'];

    /**
     * 刷新缓存
     * @param $code
     * @return bool
     */
    public static function refreshCache($code)
    {
        Cache::forget('option_dict');
        $cache = static::getCacheOrSet($code,function () use ($code) {
            $option = static::getOption($code);
            static::cacheOption($code, $option);
            return $option;
        });
        $option = static::getOption($code);
        if ($option->updated_at > $cache->updated_at) {
            static::cacheOption($code, $option);
        }
        return true;
    }

    //region 自定义缓存

    /**
     * 获取缓存
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public static function getCache(string $key = '', mixed $default = null): mixed
    {
        return static::getCacheOrSet($key,function () use ($key) {
            $option = static::getOption($key);
            static::cacheOption($key, $option);
            return $option;
        });
    }

    /**
     * 获取选项信息
     *
     * @param $code
     *
     * @return SystemOption|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|Builder|object|null
     */
    private static function getOption($code)
    {
        $data = static::where('code', $code)
            ->select(['name', 'code', 'updated_at'])
            ->with(['items'])
            ->first();
        return $data;
    }

    /**
     * 获取选项信息
     *
     * @param $code
     * @param $option
     *
     * @return SystemOption|\Illuminate\Database\Eloquent\Builder|Model|Builder|object|null
     */
    public static function cacheOption($code, $option)
    {
        static::setCache($code, $option);
        foreach ($option['items'] as $item) {
            SystemOptionItem::setCache($item->id, $item->value);
        }
        return $option;
    }

    /**
     * 选项值
     * @return HasMany
     */
    public function items()
    {
        return $this->hasMany(SystemOptionItem::class, 'code', 'code')
            ->select(['id', 'code', 'value'])
            ->orderBy('sort')
            ->orderBy('id');
    }

    //endregion

}
