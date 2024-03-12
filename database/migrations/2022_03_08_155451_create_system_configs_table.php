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
        Schema::create('system_configs', function (Blueprint $table) {
            $table->id();
            $table->string('unique')->unique()->comment('系统设置类型');
            $table->string('type')->index()->comment('系统设置类型');
            $table->string('key')->index()->comment('系统设置键');
            $table->longText('value')->comment('系统设置值');
            $table->string('data_type')->nullable()->comment('数据类型');
            $table->timestampsInteger();
            $table->softDeletesInteger();
            $table->comment('系统设置表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_configs');
    }
};
