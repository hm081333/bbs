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
        Schema::create('forum_topic_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forum_community_id')->index()->comment('版块ID');
            $table->string('name')->comment('论坛主题分类名称');
            $table->bool('is_show', true)->comment('是否显示');
            $table->sort();
            $table->timestampsInteger();
            $table->softDeletesInteger();
            $table->comment('论坛主题分类');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('forum_topic_types');
    }
};
