<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertColumnsToImsYzSupplierWithdrawTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_supplier_withdraw')) {
            Schema::table('yz_supplier_withdraw', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_supplier_withdraw', 'apply_money')) {
                    $table->decimal('apply_money', 14)->default(0.00);
                }
                if (!Schema::hasColumn('yz_supplier_withdraw', 'service_type')) {
                    $table->boolean('service_type')->default(0);
                }
                if (!Schema::hasColumn('yz_supplier_withdraw', 'service_money')) {
                    $table->integer('service_money')->default(0);
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
        Schema::table('yz_supplier_withdraw', function (Blueprint $table) {
            $table->dropColumn('apply_money');
            $table->dropColumn('service_type');
            $table->dropColumn('service_money');
        });
    }
}
