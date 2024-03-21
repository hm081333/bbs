<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_optional_funds', function (Blueprint $table) {
            $table->id();
            $table->userId()->index();
            $table->foreignId('fund_id')->index()->comment('基金ID');
            $table->string('code')->index()->comment('基金代码');
            $table->string('name')->comment('基金名称');
            $table->sort();
            $table->timestampsInteger();
            $table->softDeletesInteger();
            $table->comment('用户自选基金');
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
        Schema::dropIfExists('user_optional_funds');
    }
};
