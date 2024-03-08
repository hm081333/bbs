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
        Schema::create('intel_product_categories', function (Blueprint $table) {
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
            $table->string('unique_key')->unique()->comment('唯一标识(panel_key:language)');
            $table->foreignId('pid')->index()->comment('上级分类ID');
            $table->foreignId('level')->comment('层级，0最高');
            $table->string('panel_key')->comment('标识码');
            $table->string('name')->comment('名称');
            $table->timestampsInteger();
            $table->softDeletesInteger();
            $table->index([
                'language',
                'panel_key',
            ]);
            $table->index([
                'language',
                'pid',
            ]);
            $table->comment('Intel产品分类');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('intel_product_categories');
    }
};
