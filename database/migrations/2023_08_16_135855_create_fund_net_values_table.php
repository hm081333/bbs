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
        Schema::create('fund_net_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fund_id')->comment('基金ID');
            $table->string('code')->comment('基金代码');
            $table->string('name')->comment('基金名称');
            $table->decimal('unit_net_value', 10, 4)->comment('单位净值');
            $table->decimal('cumulative_net_value', 10, 4)->comment('累计净值');
            $table->timestamp('net_value_time')->comment('基金净值时间');
            $table->timestamps();
            //$table->softDeletes();
            $table->index([
                'fund_id',
                'net_value_time',
            ]);
        });
        // 表注释
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `ly_fund_net_values` comment '基金净值表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fund_net_values');
    }
};
