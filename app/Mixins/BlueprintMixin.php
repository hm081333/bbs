<?php

namespace App\Mixins;


use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ColumnDefinition;

class BlueprintMixin
{
    /**
     * 往数据库表添加一个新的时间戳字段
     *
     * @return \Closure
     */
    public function timestampInteger()
    {
        return function ($column): ColumnDefinition {
            /* @var $this Blueprint */
            return $this->unsignedInteger($column);
        };
    }

    /**
     * 往数据库表添加可为空的创建与删除时间时间戳字段
     *
     * @return \Closure
     */
    public function timestampsInteger()
    {
        return function () {
            /* @var $this Blueprint */
            $this->timestampInteger('created_at')->comment('创建时间');
            $this->timestampInteger('updated_at')->comment('更新时间');
        };
    }

    /**
     * 往数据库表添加软删除时间时间戳字段
     *
     * @return ColumnDefinition
     */
    public function softDeletesInteger()
    {
        return function ($column = 'deleted_at'): ColumnDefinition {
            /* @var $this Blueprint */
            return $this->timestampInteger($column)->nullable()->comment('删除时间');
        };
    }

    public function sort()
    {
        return function (): ColumnDefinition {
            /* @var $this Blueprint */
            return $this->unsignedTinyInteger('sort')->index()->default(255)->comment('排序');
        };
    }
}
