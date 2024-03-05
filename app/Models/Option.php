<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * App\Models\Option
 *
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @property-read string $sex_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OptionItem> $items
 * @property-read int|null $items_count
 * @property-write mixed $sn
 * @property-write mixed $sort
 * @method static \Illuminate\Database\Eloquent\Builder|Option newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Option newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Option onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Option query()
 * @method static \Illuminate\Database\Eloquent\Builder|Option withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Option withoutTrashed()
 * @mixin Eloquent
 */
class Option extends BaseModel
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
     * @param $code
     * @return Option|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|Builder|object|null
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
     * @param $code
     * @param $option
     * @return Option|\Illuminate\Database\Eloquent\Builder|Model|Builder|object|null
     */
    public static function cacheOption($code, $option)
    {
        static::setCache($code, $option);
        foreach ($option['items'] as $item) {
            OptionItem::setCache($item->id, $item->value);
        }
        return $option;
    }

    /**
     * 选项值
     * @return HasMany
     */
    public function items()
    {
        return $this->hasMany(OptionItem::class, 'code', 'code')
            ->select(['id', 'code', 'value'])
            ->orderBy('sort')
            ->orderBy('id');
    }

    //endregion

}
