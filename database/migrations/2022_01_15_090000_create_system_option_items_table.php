<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_option_items', function (Blueprint $table) {
            $table->id();
            $table->string('code')->comment('选项编码');
            // $table->string('key')->comment('选项键');
            $table->string('value')->comment('选项值');
            // $table->string('status')->comment('选项状态');
            $table->sort();
            $table->timestampsInteger();
            $table->softDeletesInteger();
            $table->comment('系统选项值表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_option_items');
    }
};
