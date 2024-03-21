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
            $table->userId()->index();
            $table->foreignId('fund_id')->index()->comment('基金ID');
            $table->string('code')->index()->comment('基金代码');
            $table->string('name')->comment('基金名称');
            $table->decimal('hold_cost', 10, 4)->comment('持有成本');
            $table->decimal('hold_share', 20, 4)->comment('持有份额');
            $table->decimal('hold_amount', 20, 4)->comment('持有金额');
            $table->decimal('hold_income', 20, 4)->comment('持有收益');
            $table->sort();
            $table->timestampsInteger();
            $table->softDeletesInteger();
            $table->comment('用户基金表');
            $table->unique([
                'user_id',
                'fund_id',
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
        Schema::dropIfExists('user_funds');
    }
};
