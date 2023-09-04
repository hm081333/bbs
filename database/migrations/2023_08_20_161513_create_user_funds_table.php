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
        Schema::create('user_funds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index()->comment('用户ID');
            $table->foreignId('fund_id')->index()->comment('基金ID');
            $table->string('code')->index()->comment('基金代码');
            $table->string('name')->comment('基金名称');
            $table->decimal('cost', 10, 4)->comment('持有成本');
            $table->decimal('share', 20, 4)->comment('持有份额');
            $table->decimal('amount', 20, 4)->comment('持有金额');
            $table->unsignedInteger('created_at')->nullable()->comment('创建时间');
            $table->unsignedInteger('updated_at')->nullable()->comment('更新时间');
            $table->unsignedInteger('deleted_at')->nullable()->comment('删除时间');
            $table->comment('用户基金表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_funds');
    }
};
