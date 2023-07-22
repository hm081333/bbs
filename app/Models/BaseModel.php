<?php

namespace App\Models;

use App\Events\ModelSavedEvent;
use App\Traits\Model\ModelBelongsTo;
use App\Traits\Model\ModelGetAttribute;
use App\Traits\Model\ModelSaveData;
use App\Traits\Model\ModelSetAttribute;
use DateTimeInterface;
use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Str;

/**
 * App\Models\BaseModel
 *
 * @property-write mixed $sort
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel withoutTrashed()
 * @mixin Eloquent
 */
class BaseModel extends \Illuminate\Database\Eloquent\Model
{
    //use HasFactory;
    use SoftDeletes, ModelGetAttribute, ModelSetAttribute, ModelBelongsTo, ModelSaveData;

    //region 类属性
    /**
     * 不能被批量赋值的属性
     * @desc 该属性设置为空时，会使create尝试保存任意属性
     * @var array
     */
    protected $guarded = [];
    //endregion

    // region 重写方法
    public function __construct(array $attributes = [])
    {
        // 添加 序列化隐藏的属性
        $this->hidden[] = 'deleted_at';
        // 添加 模型的事件映射
        $this->dispatchesEvents['saved'] = ModelSavedEvent::class;
        parent::__construct($attributes);
    }

    /**
     * 为 array / JSON 序列化准备日期格式
     * @param DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Convert the model's attributes to an array.
     *
     * @return array
     */
    public function attributesToArray()
    {
        // If an attribute is a date, we will cast it to a string after converting it
        // to a DateTime / Carbon instance. This is so we will get some consistent
        // formatting while accessing attributes vs. arraying / JSONing a model.
        $attributes = $this->addDateAttributesToArray(
            $attributes = $this->getArrayableAttributes()
        );

        $attributes = $this->addMutatedAttributesToArray(
            $attributes, $mutatedAttributes = $this->getMutatedAttributes()
        );

        // Next we will handle any casts that have been setup for this model and cast
        // the values to their appropriate type. If the attribute has a mutator we
        // will not perform the cast on those attributes to avoid any confusion.
        $attributes = $this->addCastAttributesToArray(
            $attributes, $mutatedAttributes
        );

        // Here we will grab all of the appended, calculated attributes to this model
        // as these attributes are not really in the attributes array, but are run
        // when we need to array or JSON the model for convenience to the coder.
        foreach ($this->getArrayableAppends() as $key) {
            $attributes[$key] = $this->mutateAttributeForArray($key, null);
        }

        foreach ($this->accepts as $key) {
            $attributes[$key] = $this->getAttribute($key);
        }

        return $attributes;
    }
    // endregion

    //region 自定义方法

    //region 追加属性相关
    /**
     * 接受属性，可动态添加的属性key
     * @var array
     */
    protected array $accepts = [];

    /**
     * 追加接受属性
     * @param $attributes
     * @return $this
     */
    public function accept($attributes): static
    {
        $this->accepts = array_unique(
            array_merge($this->accepts, is_string($attributes) ? func_get_args() : $attributes)
        );
        return $this;
    }

    /**
     * 获取接受属性
     * @return array
     */
    public function getAccepts()
    {
        return $this->accepts;
    }

    /**
     * 设置接受属性
     * @param array $accepts
     * @return $this
     */
    public function setAccepts(array $accepts): static
    {
        $this->accepts = $accepts;
        return $this;
    }

    //endregion

    /**
     * 参数数组，用于存储获取器数据，避免重复查询数据库
     * @var array
     */
    protected array $tempValues = [];

    /**
     * 是否存在数据
     * @param $key
     * @return bool
     */
    protected function hasTempValue($key): bool
    {
        return isset($this->tempValues[$key]);
    }

    /**
     * 获取数据
     * @param $key
     * @param $default
     * @return mixed|null
     */
    protected function getTempValue($key, $default = null): mixed
    {
        return $this->tempValues[$key] ?? $default;
    }

    /**
     * 设置数据
     * @param $key
     * @param $value
     * @return $this
     */
    protected function setTempValue($key, $value): static
    {
        $this->tempValues[$key] = $value;
        return $this;
    }

    /**
     * 缓存键
     * @param string $key
     * @return string
     */
    public static function getCacheKey(string $key = ''): string
    {
        return Str::snake(Str::pluralStudly(class_basename(static::class))) . (empty($key) ? '' : (':' . $key));
    }

    /**
     * 获取值变动数组
     * @return array
     */
    public function getColumnChanges(): array
    {
        $changes = [];
        $updated_at = $this->getAttribute('updated_at')->format('Y-m-d H:i:s');
        foreach ($this->getChanges() as $column => $new_value) {
            if (!in_array($column, [
                'created_at',
                'updated_at',
                'deleted_at',
            ])) {
                $old_value = $this->getRawOriginal($column);
                $changes[] = [
                    'column' => $column,
                    'old_value' => $old_value,
                    'new_value' => $new_value,
                    'created_at' => $updated_at,
                    'updated_at' => $updated_at,
                ];
            }
        }
        return $changes;
    }
    //endregion

}