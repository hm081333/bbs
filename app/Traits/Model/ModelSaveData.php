<?php

namespace App\Traits\Model;

use Exception;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait ModelSaveData
{
    public $relationData = [];

    /**
     * 获取关联数据
     * @return array|mixed
     */
    public function getRelationData()
    {
        return $this->relationData;
    }

    /**
     * 设置关联数据
     * @param $relation
     * @param $data
     * @return void
     */
    public function setRelationData($relation, $data)
    {
        $this->relationData[$relation] = $data;
    }

    /**
     * 清除关联数据
     * @return void
     */
    public function clearRelationData()
    {
        $this->relationData = [];
    }

    /**
     * 批量保存数据对象值
     * @param array $data
     * @return bool
     */
    public function saveData(array $data)
    {
        return $this->appendData($data)->save();
    }

    /**
     * 批量追加数据对象值
     * @access public
     * @param array $data
     * @return $this
     */
    public function appendData(array $data)
    {
        foreach ($data as $key => $item) {
            if (method_exists($this, $key) && is_array($item)) {
                try {
                    if (in_array(get_class($this->$key()), [
                        HasMany::class,
                        HasOne::class,
                    ])) {
                        $this->relationData[$key] = $item;
                        // unset($data[$key]);
                    }
                } catch (Exception $e) {
                    // dump($e->getMessage());
                }
            } else {
                $this->setAttribute($key, $item);
            }
        }
        return $this;
    }
}
