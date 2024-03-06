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
            $table->string('language', 10)->comment('规格语言');
            $table->string('unique_key')->unique()->comment('唯一标识(ark_series_id:language)');
            $table->foreignId('category_id')->index()->comment('分类ID');
            $table->string('ark_series_id')->comment('ARK系列ID');
            $table->string('name')->comment('名称');
            $table->string('path')->comment('规格列表路径');
            $table->string('url')->comment('规格列表URL');
            $table->timestampsInteger();
            $table->softDeletesInteger();
            $table->index([
                'language',
                'ark_series_id',
            ]);
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
