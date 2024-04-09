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
        Schema::create('user_notify_push_plus_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->nullable()->comment('用户ID');
            $table->bool('enable')->comment('开启推送');
            $table->string('token')->comment('TOKEN');
            $table->string('topic')->nullable()->comment('群组编码');
            $table->timestampsInteger();
            $table->comment('用户Push Plus推送设置');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_notify_push_plus_settings');
    }
};
