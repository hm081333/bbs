<?php

namespace App\Traits\Model;

use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

trait ModelSaveData
{
    private Collection $relationData;

    /**
     * 获取关联数据
     *
     * @return Collection
     */
    public function getRelationData()
    {
        return collect($this->relationData);
    }

    /**
     * 设置关联数据
     *
     * @param $relation
     * @param $data
     *
     * @return void
     */
    public function setRelationData($relation, $data)
    {
        $this->relationData->put($relation, $data);
    }

    /**
     * 清除关联数据
     *
     * @return void
     */
    public function clearRelationData(): void
    {
        $this->relationData = collect();
    }

    /**
     * 批量保存数据对象值
     *
     * @param array|Collection $data
     *
     * @return bool|bool[]
     */
    public function saveData(array|Collection $data)
    {
        if (is_array($data) && Arr::isAssoc($data)) return $this->appendData($data)->save();
        return array_map(fn($attributes) => (clone $this)->appendData($attributes)->save(), $data instanceof Collection ? $data->toArray() : $data);
    }

    /**
     * 批量追加数据对象值
     *
     * @access public
     *
     * @param array $data
     *
     * @return $this
     */
    public function appendData(array $data): static
    {
        $this->clearRelationData();
        foreach ($data as $column => $value) {
            if (method_exists($this, $column) && is_array($value)) {
                try {
                    if (in_array(get_class($this->$column()), [
                        HasMany::class,
                        HasOne::class,
                        BelongsTo::class,
                        BelongsToMany::class,
                    ])) {
                        $this->setRelationData($column, $value);
                        // unset($data[$column]);
                    }
                } catch (Exception $e) {
                    // dump($e->getMessage());
                }
            } else {
                $this->setAttribute($column, $value);
            }
        }
        return $this;
    }
}
