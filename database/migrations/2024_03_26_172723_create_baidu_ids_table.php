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
        Schema::create('baidu_ids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index()->comment('用户ID');
            $table->text('bduss')->comment('百度cookie');
            $table->string('name')->comment('百度用户名');
            $table->unsignedBigInteger('bid')->default(0)->comment('百度ID');
            $table->string('stoken')->nullable()->comment('百度stoken');
            $table->string('portrait')->unique()->comment('百度账号唯一标识');
            $table->timestampInteger('refresh_time')->default(0)->comment('刷新贴吧列表时间');
            $table->timestampsInteger();
            $table->softDeletesInteger();
            $table->comment('BDUSS ID列表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('baidu_ids');
    }
};
