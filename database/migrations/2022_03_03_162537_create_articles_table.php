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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->comment('分类ID');
            $table->string('title')->comment('标题');
            $table->string('desc')->comment('简介');
            $table->string('cover')->nullable()->comment('封面图片');
            $table->longText('content')->comment('内容');
            $table->string('code')->nullable()->unique()->comment('标识码');
            $table->unsignedBigInteger('read_times')->default(0)->comment('阅读次数');
            $table->bool('is_show', true)->comment('是否显示');
            $table->sort();
            $table->timestampsInteger();
            $table->softDeletesInteger();
            $table->comment('文章表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articles');
    }
};
