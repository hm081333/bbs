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
        Schema::create('fund_valuations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fund_id')->index()->comment('基金ID');
            $table->string('code')->comment('基金代码');
            $table->string('name')->comment('基金名称');
            $table->decimal('unit_net_value', 10, 4)->comment('单位净值');
            $table->decimal('estimated_net_value', 10, 4)->comment('预估净值');
            $table->decimal('estimated_growth', 10, 4)->comment('预估增长值');
            $table->decimal('estimated_growth_rate', 10, 4)->comment('预估增长率');
            $table->unsignedInteger('valuation_time')->comment('基金估值时间');
            $table->string('valuation_source')->comment('基金估值来源');
            $table->unsignedInteger('created_at')->nullable()->comment('创建时间');
            $table->unsignedInteger('updated_at')->nullable()->comment('更新时间');
            $table->unsignedInteger('deleted_at')->nullable()->comment('删除时间');
            $table->unique([
                'fund_id',
                'valuation_time',
                'valuation_source',
            ], 'unique_key');
            // $table->index([
            //     'code',
            //     'valuation_time',
            //     'valuation_source',
            // ]);
            $table->comment('基金估值表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fund_valuations');
    }
};
