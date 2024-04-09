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
     * 往数据库表添加开关字段
     *
     * @return Closure
     */
    public function bool()
    {
        return function (string $column, bool $default = false): ColumnDefinition {
            /* @var $this Blueprint */
            return $this->boolean($column)->unsigned()->default(intval($default))->index();
        };
    }

    /**
     * 往数据库表添加操作者字段
     *
     * @return Closure
     */
    public function operator()
    {
        return function ($nullable = true) {
            /* @var $this Blueprint */
            $this->foreignId('operator_id')->nullable($nullable)->comment('操作者ID');
            $this->string('operator_type')->nullable($nullable)->comment('操作者类型');
        };
    }

    /**
     * 往数据库表添加省市区字段
     *
     * @return Closure
     */
    public function ProvinceCityDistrict()
    {
        return function ($nullable = true) {
            /* @var $this Blueprint */
            $this->foreignId('province')->nullable($nullable)->comment('省');
            $this->foreignId('city')->nullable($nullable)->comment('市');
            $this->foreignId('district')->nullable($nullable)->comment('区');
        };
    }

    /**
     * 往数据库表添加管理员关联字段
     *
     * @return Closure
     */
    public function adminId()
    {
        return function (): ColumnDefinition {
            /* @var $this Blueprint */
            return $this->foreignId('admin_id')->comment('管理员ID');
        };
    }

    /**
     * 往数据库表添加用户关联字段
     *
     * @return Closure
     */
    public function userId()
    {
        return function (): ColumnDefinition {
            /* @var $this Blueprint */
            return $this->foreignId('user_id')->comment('管理员ID');
        };
    }
}
