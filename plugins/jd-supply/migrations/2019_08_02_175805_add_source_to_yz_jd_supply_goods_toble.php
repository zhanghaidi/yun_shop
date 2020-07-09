<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSourceToYzJdSupplyGoodsToble extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_jd_supply_goods')) {
            if (!Schema::hasColumn('yz_jd_supply_goods', 'source')) {
                Schema::table('yz_jd_supply_goods', function ($table) {
                    $table->integer('source')->nullable()->default(0)->comment('第三方商品来源类型');
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
