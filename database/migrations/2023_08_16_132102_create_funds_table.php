<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('funds', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('基金代码');
            $table->string('name')->comment('基金名称');
            $table->string('pinyin_initial')->comment('基金名称拼音首字母');
            $table->string('type')->nullable()->comment('基金类型');
            $table->decimal('unit_net_value', 10, 4)->nullable()->comment('单位净值');
            $table->decimal('cumulative_net_value', 10, 4)->nullable()->comment('累计净值');
            $table->unsignedInteger('net_value_time')->nullable()->comment('基金净值时间');
            $table->unsignedInteger('created_at')->nullable()->comment('创建时间');
            $table->unsignedInteger('updated_at')->nullable()->comment('更新时间');
            $table->unsignedInteger('deleted_at')->nullable()->comment('删除时间');
            $table->comment('基金表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('funds');
    }
};
