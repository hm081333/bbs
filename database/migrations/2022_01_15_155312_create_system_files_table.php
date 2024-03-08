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
        Schema::create('system_files', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('文件名');
            $table->string('path')->unique()->comment('文件路径');
            $table->string('origin_name')->comment('原始文件名');
            $table->string('mime_type')->comment('文件类型');
            $table->string('extension')->comment('文件后缀');
            $table->string('size')->nullable()->comment('文件大小');
            $table->string('width')->nullable()->comment('宽度');
            $table->string('height')->nullable()->comment('高度');
            $table->timestampsInteger();
            $table->softDeletesInteger();
            $table->comment('系统文件表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_files');
    }
};
