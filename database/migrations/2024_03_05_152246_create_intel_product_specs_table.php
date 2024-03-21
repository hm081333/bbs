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
            $table->string('unique_key')->unique()->comment('唯一标识(ark_product_id:language:key)');
            $table->foreignId('category_id')->index()->comment('分类ID');
            $table->foreignId('series_id')->index()->comment('系列ID');
            $table->foreignId('product_id')->index()->comment('产品ID');
            $table->bigInteger('ark_series_id')->comment('ARK系列ID');
            $table->bigInteger('ark_product_id')->comment('ARK产品ID');
            $table->unsignedTinyInteger('tab_index')->comment('规格分类下标');
            $table->string('tab_title')->comment('规格分类名称');
            $table->string('key')->comment('规格键');
            $table->string('label')->comment('规格名称');
            $table->text('label_tips_rich_text')->nullable()->comment('规格名称说明（富文本内容）');
            $table->text('value')->comment('规格值');
            $table->string('value_url')->nullable()->comment('规格值绑定URL');
            $table->timestampsInteger();
            $table->softDeletesInteger();
            // $table->index([
            //     // 'category_id',
            //     // 'series_id',
            //     'product_id',
            //     'tab_index',
            //     'key',
            // ], 'id_index');
            $table->index([
                // 'ark_series_id',
                'language',
                'ark_product_id',
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
