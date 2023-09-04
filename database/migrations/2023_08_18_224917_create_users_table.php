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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('user_name')->unique()->comment('用户名');
            $table->string('nick_name')->nullable()->comment('昵称');
            $table->string('real_name')->nullable()->comment('真实名称');
            $table->string('mobile', 15)->nullable()->unique()->comment('用户手机');
            $table->string('email')->nullable()->unique()->comment('邮箱');
            $table->unsignedInteger('email_verified_at')->nullable()->comment('邮箱验证时间');
            $table->string('previous_avatar')->nullable()->comment('上一张头像');
            $table->string('avatar')->nullable()->comment('头像');
            $table->tinyInteger('sex')->default(0)->comment('性别 0保密1男2女');
            $table->unsignedInteger('birthdate')->nullable()->comment('出生日期');
            $table->unsignedInteger('last_login_time')->nullable()->comment('最后登录时间');
            $table->tinyInteger('status')->default(1)->comment('状态 1正常 2冻结');
            $table->unsignedInteger('frozen_time')->nullable()->comment('冻结时间');
            $table->string('open_id')->nullable()->comment('微信唯一ID');
            $table->string('password')->comment('密码');
            $table->text('o_pwd')->nullable()->comment('原始密码');
            $table->rememberToken();
            $table->unsignedInteger('created_at')->nullable()->comment('创建时间');
            $table->unsignedInteger('updated_at')->nullable()->comment('更新时间');
            $table->unsignedInteger('deleted_at')->nullable()->comment('删除时间');
            $table->comment('用户表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
