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
        Schema::create('advs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->index()->comment('分类ID');
            $table->string('title')->comment('广告标题');
            $table->string('image')->comment('广告图片');
            $table->string('url')->nullable()->comment('广告链接');
            $table->bool('is_show', true)->comment('是否显示');
            $table->sort();
            $table->timestampsInteger();
            $table->softDeletesInteger();
            $table->comment('广告表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('advs');
    }
};
