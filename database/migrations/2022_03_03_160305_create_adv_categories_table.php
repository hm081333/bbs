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
        Schema::create('adv_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('广告分类名称');
            $table->string('code')->nullable()->unique()->comment('广告分类标识码');
            $table->foreignId('pid')->index()->default(0)->comment('分类父级ID');
            $table->sort();
            $table->timestampsInteger();
            $table->softDeletesInteger();
            $table->comment('广告分类表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adv_categories');
    }
};
