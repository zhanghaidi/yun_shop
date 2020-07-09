<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldToYzLoveGoods2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_love_goods')) {
            Schema::table('yz_love_goods', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_love_goods', 'second_award_proportion')) {
                    $table->decimal('second_award_proportion', 10, 2);
                }
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
        //
    }
}
