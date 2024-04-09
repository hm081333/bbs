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
        Schema::create('user_notify_bark_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->nullable()->comment('用户ID');
            $table->bool('enable')->comment('开启推送');
            $table->string('server_address')->comment('服务器地址');
            $table->string('device_key')->comment('设备Key');
            $table->string('level')->nullable()->comment('推送中断级别');
            $table->string('sound')->nullable()->comment('推送声音');
            $table->string('base_group')->nullable()->comment('消息分组');
            $table->timestampsInteger();
            $table->comment('用户BARK推送设置');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_notify_bark_settings');
    }
};
