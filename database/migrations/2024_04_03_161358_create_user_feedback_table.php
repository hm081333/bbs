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
        Schema::create('user_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->comment('用户ID');
            // $table->string('mobile')->comment('联系电话');
            $table->longText('content')->comment('反馈内容');
            $table->json('images')->nullable()->comment('图片');
            $table->string('contact')->nullable()->comment('联系方式');
            $table->unsignedTinyInteger('status')->default(0)->comment('状态：0待处理1已处理2已打回');
            $table->timestampInteger('dispose_time')->nullable()->comment('处理时间');
            $table->timestampsInteger();
            $table->softDeletesInteger();
            $table->comment('用户反馈');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_feedback');
    }
};
