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
        Schema::create('user_notify_ding_ding_bot_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->nullable()->comment('用户ID');
            $table->bool('enable')->comment('开启推送');
            $table->string('token')->comment('钉钉机器人TOKEN');
            $table->string('secret')->nullable()->comment('钉钉机器人秘钥');
            $table->timestampsInteger();
            $table->comment('用户钉钉机器人推送设置');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_notify_ding_ding_bot_settings');
    }
};
