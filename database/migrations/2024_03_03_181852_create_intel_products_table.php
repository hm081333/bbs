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
        Schema::create('intel_products', function (Blueprint $table) {
            $table->id();
            $table->enum('language', [
                'id_id',
                'de_de',
                'en_us',
                'es_xl',
                'fr_fr',
                'pt_br',
                'vi_vn',
                'th_th',
                'ko_kr',
                'ja_jp',
                'zh_cn',
                'zh_tw',
            ])->comment('语言');
            $table->string('unique_key')->unique()->comment('唯一标识(ark_product_id:language)');
            $table->foreignId('category_id')->index()->comment('分类ID');
            $table->foreignId('series_id')->index()->comment('系列ID');
            $table->string('ark_series_id')->comment('ARK系列ID');
            $table->string('ark_product_id')->comment('ARK产品ID');
            $table->string('name')->comment('名称');
            $table->string('path')->comment('规格列表路径');
            $table->string('url')->comment('规格列表URL');
            $table->timestampsInteger();
            $table->softDeletesInteger();
            $table->index([
                'language',
                'ark_series_id',
                'ark_product_id',
            ]);
            $table->index([
                'category_id',
                'series_id',
            ]);
            $table->comment('Intel产品');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('intel_products');
    }
};
