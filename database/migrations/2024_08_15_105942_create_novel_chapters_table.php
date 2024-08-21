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
        Schema::create('novel_chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('novel_id')->index()->comment('小说ID');
            $table->string('title')->comment('标题');
            $table->longText('content')->nullable()->comment('内容');
            $table->string('source_path')->comment('来源路径');
            $table->string('source_url')->comment('来源地址')->unique();
            $table->timestampsInteger();
            $table->softDeletesInteger();
            $table->comment('小说章节');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('novel_chapters');
    }
};
