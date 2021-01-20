<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYzMrytLevel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_mryt_level')) {
            Schema::create('yz_mryt_level', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid');
                $table->integer('level_weight')->default(0);
                $table->string('level_name', 45);
                $table->string('team_manage_ratio');
                $table->decimal('team', 15, 2)->default(0);
                $table->decimal('thankful', 15, 2)->default(0);
                $table->string('train_ratio', 45);
                $table->decimal('direct', 15, 2)->default(0);
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
        Schema::dropIfExists('yz_mryt_level');
    }
}
