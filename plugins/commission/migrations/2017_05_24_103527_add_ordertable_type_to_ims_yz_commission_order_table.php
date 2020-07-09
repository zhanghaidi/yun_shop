<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrdertableTypeToImsYzCommissionOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('yz_commission_order', function(Blueprint $table)
        {
            if (Schema::hasColumn('yz_commission_order', 'type')) {
                $table->renameColumn('type', 'ordertable_type');
            }

            if (Schema::hasColumn('yz_commission_order', 'type_id')) {
                $table->renameColumn('type_id', 'ordertable_id');
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
        Schema::table('yz_commission_order', function (Blueprint $table) {
            if (Schema::hasColumn('yz_commission_order', 'ordertable_type')) {
                $table->renameColumn('ordertable_type', 'type');
            }

            if (Schema::hasColumn('yz_commission_order', 'ordertable_id')) {
                $table->renameColumn('ordertable_id', 'type_id');
            }
        });
    }
}
