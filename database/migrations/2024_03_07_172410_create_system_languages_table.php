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
        Schema::create('system_languages', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->comment('标识');
            $table->string('name')->comment('名称');
            $table->string('locale')->comment('语言代号');
            $table->timestampsInteger();
            $table->softDeletesInteger();
            $table->comment('系统语言表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_languages');
    }
};
