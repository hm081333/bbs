<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('intel_product_specs', function (Blueprint $table) {
            $table->id();
            $table->string('unique_key')->unique()->comment('唯一标识(ark_product_id:language:key)');
            $table->foreignId('category_id')->comment('分类ID');
            $table->foreignId('series_id')->comment('系列ID');
            $table->foreignId('product_id')->comment('产品ID');
            $table->string('ark_series_id')->comment('ARK系列ID');
            $table->string('ark_product_id')->comment('ARK产品ID');
            $table->string('language', 10)->comment('规格语言');
            $table->unsignedTinyInteger('tab_index')->comment('规格分类下标');
            $table->string('tab_title')->comment('规格分类名称');
            $table->string('key')->comment('规格键');
            $table->string('label')->comment('规格名称');
            $table->string('value')->comment('规格值');
            $table->string('value_url')->nullable()->comment('规格值绑定URL');
            $table->timestampsInteger();
            $table->softDeletesInteger();
            $table->index([
                // 'category_id',
                // 'series_id',
                'product_id',
                'language',
                'tab_index',
                'key',
            ], 'id_index');
            $table->index([
                // 'ark_series_id',
                'ark_product_id',
                'language',
                'tab_index',
                'key',
            ], 'ark_id_index');
            $table->comment('Intel产品规格');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('intel_product_specs');
    }
};
