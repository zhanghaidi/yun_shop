<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGoodsIdImsYzDrawActivityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_draw_activity')) {
            Schema::table('yz_draw_activity', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_draw_activity', 'goods_id')) {
                    $table->integer('goods_id')->nullable();
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
