<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLoveGoodsLoveAccelerate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_love_goods')) {
            if (!Schema::hasColumn('yz_love_goods', 'love_accelerate')) {
                Schema::table('yz_love_goods', function (Blueprint $table) {
                    $table->integer('love_accelerate')->default(0);
                    $table->integer('activation_state')->default(0);
                    $table->integer('deduction_proportion_low')->default(0);
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
