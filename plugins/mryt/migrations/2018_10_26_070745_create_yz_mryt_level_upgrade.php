<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYzMrytLevelUpgrade extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_mryt_level_upgrade')) {
            Schema::create('yz_mryt_level_upgrade', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid');
                $table->integer('level_id')->default(0);
                $table->text('parase');
                $table->integer('created_at')->unsigned()->nullable();
                $table->integer('updated_at')->unsigned()->nullable();
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
        Schema::dropIfExists('yz_mryt_level_upgrade');
    }
}
