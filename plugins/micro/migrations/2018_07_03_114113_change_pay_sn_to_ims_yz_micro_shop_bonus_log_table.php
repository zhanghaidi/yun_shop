<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePaySnToImsYzMicroShopBonusLogTable extends Migration
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
                if (Schema::hasColumn('yz_micro_shop_bonus_log', 'pay_sn')) {
                    $table->string('pay_sn', 50)->nullable()->default('')->change();
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
            if (Schema::hasColumn('yz_micro_shop_bonus_log', 'pay_sn')) {
                $table->integer('pay_sn')->change();
            }
        });
    }
}