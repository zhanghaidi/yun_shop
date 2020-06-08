<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeAmountToYzAlipayOrderSettleLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_alipay_order_settle_log')) {
            if (Schema::hasColumn('yz_alipay_order_settle_log', 'amount')) {
                Schema::table('yz_alipay_order_settle_log', function (Blueprint $table) {
                    $table->decimal('amount', 11,2)->change();
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
