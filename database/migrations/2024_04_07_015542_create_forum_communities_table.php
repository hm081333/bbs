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
        Schema::create('forum_communities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pid')->default(0)->comment('版块父级ID');
            $table->unsignedTinyInteger('level')->default(0)->comment('版块层级，0：父版块，1：子版块');
            $table->string('name')->comment('版块名称');
            $table->string('desc')->default('')->comment('版块描述');
            $table->bool('is_show', true)->comment('是否显示');
            $table->sort();
            $table->timestampsInteger();
            $table->softDeletesInteger();
            $table->comment('论坛版块');
            $table->index([
                'pid',
                'level',
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
        Schema::dropIfExists('forum_communities');
    }
};
