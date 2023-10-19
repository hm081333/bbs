<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('文件名');
            $table->string('path')->unique()->comment('文件路径');
            $table->string('origin_name')->comment('原始文件名');
            $table->string('mime_type')->comment('文件类型');
            $table->string('extension')->comment('文件后缀');
            $table->string('size')->nullable()->comment('文件大小');
            $table->string('width')->nullable()->comment('宽度');
            $table->string('height')->nullable()->comment('高度');
            $table->unsignedInteger('created_at')->nullable()->comment('创建时间');
            $table->unsignedInteger('updated_at')->nullable()->comment('更新时间');
            $table->unsignedInteger('deleted_at')->nullable()->comment('删除时间');
            $table->comment('文件表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
