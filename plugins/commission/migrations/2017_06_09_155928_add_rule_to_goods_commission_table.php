<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRuleToGoodsCommissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     * 
     */
    public function up()
    {
        Schema::table('yz_goods_commission', function (Blueprint $table) {
            if (!Schema::hasColumn('yz_goods_commission', 'rule')) {
                $table->text('rule', 65535)->nullable();
            }
            if (Schema::hasColumn('yz_goods_commission', 'first_level_rate')) {
                $table->dropColumn('first_level_rate');
            }
            if (Schema::hasColumn('yz_goods_commission', 'first_level_pay')) {
                $table->dropColumn('first_level_pay');
            }
            if (Schema::hasColumn('yz_goods_commission', 'second_level_rate')) {
                $table->dropColumn('second_level_rate');
            }
            if (Schema::hasColumn('yz_goods_commission', 'second_level_pay')) {
                $table->dropColumn('second_level_pay');
            }
            if (Schema::hasColumn('yz_goods_commission', 'third_level_rate')) {
                $table->dropColumn('third_level_rate');
            }
            if (Schema::hasColumn('yz_goods_commission', 'third_level_pay')) {
                $table->dropColumn('third_level_pay');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('yz_goods_commission', function (Blueprint $table) {
            $table->dropColumn('rule');
        });
    }
}
