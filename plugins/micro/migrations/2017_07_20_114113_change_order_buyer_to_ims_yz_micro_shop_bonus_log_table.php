<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeOrderBuyerToImsYzMicroShopBonusLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (\Schema::hasTable('yz_micro_shop_bonus_log')) {
            Schema::table('yz_micro_shop_bonus_log', function (Blueprint $table) {
                if (Schema::hasColumn('yz_micro_shop_bonus_log', 'order_buyer')) {
                    $table->string('order_buyer', 255)->change();
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
        Schema::table('yz_micro_shop_bonus_log', function (Blueprint $table) {
            if (Schema::hasColumn('yz_micro_shop_bonus_log', 'order_buyer')) {
                $table->integer('order_buyer')->change();
            }
        });
    }
}