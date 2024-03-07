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
        Schema::create('administrative_divisions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('名称');
            $table->string('attr')->comment('简称');
            $table->unsignedBigInteger('code')->unique()->comment('编码');
            $table->string('initial')->index()->nullable()->comment('首字母');
            $table->foreignId('pid')->index()->default(0)->comment('父级ID，0代表顶级，其他关联');
            $table->unsignedTinyInteger('level')->index()->default(0)->comment('层级，0最高');
            $table->sort();
            $table->decimal('lat', 16, 12)->nullable()->comment('中心点纬度');
            $table->decimal('lng', 16, 12)->nullable()->comment('中心点经度');
            $table->timestampsInteger();
            $table->softDeletesInteger();
            $table->comment('行政区划表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('administrative_divisions');
    }
};
