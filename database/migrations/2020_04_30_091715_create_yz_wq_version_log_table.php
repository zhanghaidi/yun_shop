<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYzWqVersionLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_wq_version_log')) {
            Schema::create('yz_wq_version_log', function (Blueprint $table) {
                $table->increments('id');
                $table->string('version')->comment('执行过的版本号');
                $table->integer('created_at')->nullable();
                $table->integer('updated_at')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('yz_wq_version_log');
    }
}
