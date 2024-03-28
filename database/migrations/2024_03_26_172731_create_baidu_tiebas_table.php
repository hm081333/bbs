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
        Schema::create('baidu_tiebas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index()->comment('用户ID');
            $table->foreignId('baidu_id')->comment('百度账号ID');
            $table->unsignedBigInteger('fid')->default(0)->comment('贴吧ID');
            $table->string('tieba')->comment('贴吧名');
            $table->unsignedTinyInteger('no')->default('0')->comment('忽略签到 0 否 1忽略');
            $table->unsignedBigInteger('status')->default(0)->comment('签到状态');
            $table->timestampInteger('latest')->default(0)->comment('最后签到时间');
            $table->text('last_error')->nullable()->comment('最近一次签到错误');
            $table->timestampInteger('refresh_time')->default(0)->comment('刷新贴吧列表时间');
            $table->timestampsInteger();
            $table->softDeletesInteger();
            $table->comment('百度贴吧列表');
            $table->index([
                'baidu_id',
                'tieba',
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
        Schema::dropIfExists('baidu_tiebas');
    }
};
