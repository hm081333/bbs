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
        Schema::create('intel_product_series', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->index()->comment('分类ID');
            $table->string('ark_series_id')->unique()->comment('ARK系列ID');
            $table->string('name')->comment('名称');
            $table->string('chinese_name')->comment('中文名称');
            $table->json('multilingual_name')->comment('多语言名称');
            $table->json('path')->comment('规格列表路径');
            $table->json('url')->comment('规格列表URL');
            $table->timestampsInteger();
            $table->softDeletesInteger();
            $table->comment('Intel产品系列');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('intel_product_series');
    }
};
