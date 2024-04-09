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
        Schema::create('forum_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index()->comment('用户ID');
            $table->foreignId('forum_community_id')->comment('版块ID');
            $table->foreignId('forum_topic_type_id')->comment('主题分类ID');
            $table->string('title')->comment('主题标题');
            $table->longText('content')->comment('主题内容');
            $table->bool('is_top', false)->comment('是否顶置');
            $table->bool('is_show', true)->comment('是否显示');
            $table->sort();
            $table->timestampsInteger();
            $table->softDeletesInteger();
            $table->comment('论坛主题');
            $table->index([
                'forum_community_id',
                'forum_topic_type_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('forum_topics');
    }
};
