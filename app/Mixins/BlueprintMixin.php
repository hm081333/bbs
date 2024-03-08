<?php

namespace App\Mixins;


use Closure;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ColumnDefinition;

class BlueprintMixin
{
    /**
     * 往数据库表添加可为空的创建与删除时间时间戳字段
     *
     * @return Closure
     */
    public function timestampsInteger()
    {
        return function (bool $nullable = false) {
            /* @var $this Blueprint */
            $this->timestampInteger('created_at')->nullable($nullable)->comment('创建时间');
            $this->timestampInteger('updated_at')->nullable($nullable)->comment('更新时间');
        };
    }

    /**
     * 往数据库表添加一个新的时间戳字段
     *
     * @return ColumnDefinition
     */
    public function timestampInteger()
    {
        return function (string $column, bool $nullable = false): ColumnDefinition {
            /* @var $this Blueprint */
            return $this->unsignedInteger($column)->nullable($nullable);
        };
    }

    /**
     * 往数据库表添加软删除时间时间戳字段
     *
     * @return ColumnDefinition
     */
    public function softDeletesInteger()
    {
        return function (string $column = 'deleted_at', bool $nullable = true): ColumnDefinition {
            /* @var $this Blueprint */
            return $this->timestampInteger($column)->nullable($nullable)->comment('删除时间');
        };
    }

    /**
     * 往数据库表添加排序字段
     *
     * @return Closure
     */
    public function sort()
    {
        return function (string $column = 'sort'): ColumnDefinition {
            /* @var $this Blueprint */
            return $this->unsignedTinyInteger($column)->index()->default(255)->comment('排序');
        };
    }

    /**
     * 往数据库表添加省市区字段
     *
     * @return Closure
     */
    public function ProvinceCityDistrict()
    {
        return function ($nullable = true): ColumnDefinition {
            /* @var $this Blueprint */
            $this->foreignId('province')->nullable($nullable)->comment('省');
            $this->foreignId('city')->nullable($nullable)->comment('市');
            $this->foreignId('district')->nullable($nullable)->comment('区');
        };
    }
}
