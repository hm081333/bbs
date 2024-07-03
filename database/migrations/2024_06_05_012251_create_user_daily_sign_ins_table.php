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
        Schema::create('user_daily_sign_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->comment('用户ID');
            $table->integer('consecutive_sign_in_days')->default(0)->comment('连续签到天数');
            $table->integer('total_consecutive_sign_in_days')->default(0)->comment('总连续签到天数');
            $table->integer('sign_in_days')->default(0)->comment('签到天数');
            $table->integer('total_sign_in_days')->default(0)->comment('总签到天数');
            $table->timestampsInteger();
            $table->softDeletesInteger();
            $table->comment('用户每日签到表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_daily_sign_ins');
    }
};
