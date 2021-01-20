<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddYzMrytVipAddup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('yz_mryt_vip_addup')) {
            Schema::create('yz_mryt_vip_addup', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('uniacid');
                $table->integer('uid')->default(0);
                $table->integer('nums')->default(0);
                $table->string('curr_date', 6)->default('000000');
                $table->integer('created_at')->unsigned()->nullable();
                $table->integer('updated_at')->unsigned()->nullable();
                $table->index(['curr_date', 'uid']);
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
        Schema::dropIfExists('yz_mryt_vip_addup');
    }
}
