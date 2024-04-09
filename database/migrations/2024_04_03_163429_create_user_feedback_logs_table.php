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
        Schema::create('user_feedback_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_feedback_id')->comment('用户反馈ID');
            $table->unsignedTinyInteger('status')->default(0)->comment('状态：0待处理1已处理2已打回');
            $table->longText('remark')->comment('备注');
            $table->operator();
            $table->timestampsInteger();
            $table->softDeletesInteger();
            $table->comment('用户反馈日志');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_feedback_logs');
    }
};
