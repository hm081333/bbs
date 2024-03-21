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
        Schema::create('user_login_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->comment('用户ID');
            $table->string('ip')->comment('登录ip');
            $table->text('user_agent')->nullable()->comment('User-Agent，用户代理');
            $table->enum('device_type', \App\Utils\Tools::getDeviceTypes())->comment('登录设备类型');
            $table->timestampInteger('quit_time')->nullable()->comment('退出时间');
            $table->integer('length_time')->default(0)->comment('登录时长（秒）');
            $table->timestampsInteger();
            // $table->softDeletesInteger();
            $table->comment('用户登录日志表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_login_logs');
    }
};
