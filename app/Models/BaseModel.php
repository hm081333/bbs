<?php

namespace App\Models;

use App\Events\ModelSavedEvent;
use App\Traits\Model\ModelBelongsTo;
use App\Traits\Model\ModelSaveData;
use App\Traits\Model\ModelSetAttribute;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Str;

class BaseModel extends Model
{
    use HasFactory, SoftDeletes, ModelSetAttribute, ModelBelongsTo, ModelSaveData;

    //region 类属性

    /**
     * 不能被批量赋值的属性
     * @var array
     */
    protected $guarded = [];

    /**
     * 应该为序列化隐藏的属性。
     * @var array<int, string>
     */
    protected $hidden = [
        // 'created_at',
        // 'updated_at',
        // 'deleted_at',
    ];

    protected $dispatchesEvents = [
        'saved' => ModelSavedEvent::class,
    ];


    //endregion

    /**
     * 缓存键
     * @param $code
     * @return string
     */
    public static function getCacheKey($key = false)
    {
        return Str::snake(Str::pluralStudly(class_basename(static::class))) . ($key ? (':' . $key) : '');
    }

    //region 模型关联
    //endregion

    //region 修改器
    //endregion

    //region 获取器
    //endregion

    /**
     * 获取选项值数据
     * @param $id
     * @return mixed
     */
    public static function getOptionItemValue($id)
    {
        return OptionItem::getValue($id);
    }

    /**
     * 为 array / JSON 序列化准备日期格式
     *
     * @param DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * 获取值变动数组
     * @return array
     */
    public function getColumnChanges()
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

}
