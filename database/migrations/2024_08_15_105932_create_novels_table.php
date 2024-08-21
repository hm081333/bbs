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
        Schema::create('novels', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('标题');
            $table->string('author')->comment('作者')->index();
            $table->string('category')->comment('分类')->index();
            $table->timestampInteger('last_updated_time')->comment('最后更新时间');
            $table->text('intro')->comment('介绍');
            $table->string('source_url')->comment('来源地址')->index();
            $table->string('source_detail_path')->comment('详情来源路径');
            $table->string('source_detail_url')->comment('详情来源地址')->unique();
            $table->index([
                'source_url',
                'source_detail_path',
            ]);
            $table->timestampsInteger();
            $table->softDeletesInteger();
            $table->comment('小说');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('novels');
    }
};
