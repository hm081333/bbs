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
        Schema::create('forum_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index()->comment('用户ID');
            $table->foreignId('forum_topic_id')->index()->comment('主题ID');
            $table->longText('content')->comment('回复内容');
            $table->bool('is_top', false)->comment('是否顶置');
            $table->bool('is_show', true)->comment('是否显示');
            $table->sort();
            $table->timestampsInteger();
            $table->softDeletesInteger();
            $table->comment('论坛回复');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('forum_replies');
    }
};
