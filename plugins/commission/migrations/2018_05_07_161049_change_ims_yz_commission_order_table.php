<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeImsYzCommissionOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_commission_order')) {
            Schema::table('yz_commission_order', function (Blueprint $table) {
                if (Schema::hasColumn('yz_commission_order', 'commission_rate')) {
                    $table->decimal('commission_rate', 14,2)->nullable()->change();
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
        Schema::dropIfExists('yz_commission_order_goods');
    }
}
