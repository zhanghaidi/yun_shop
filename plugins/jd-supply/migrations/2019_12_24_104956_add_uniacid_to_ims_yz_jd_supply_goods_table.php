<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUniacidToImsYzJdSupplyGoodsTable extends Migration
{
    public function up()
    {

        if (Schema::hasTable('yz_jd_supply_goods')) {
            if (!Schema::hasColumn('yz_jd_supply_goods', 'uniacid')) {
                Schema::table('yz_jd_supply_goods', function (Blueprint $table) {
                    $table->integer('uniacid')->nullable();
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

    }
}
