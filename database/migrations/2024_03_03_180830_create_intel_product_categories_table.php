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
        Schema::create('intel_product_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pid')->index()->comment('上级分类ID');
            $table->foreignId('level')->comment('层级，0最高');
            $table->string('panel_key')->unique()->comment('标识码');
            $table->string('name')->comment('名称');
            $table->string('chinese_name')->comment('中文名称');
            $table->json('multilingual_name')->comment('多语言名称');
            $table->timestampsInteger();
            $table->softDeletesInteger();
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
