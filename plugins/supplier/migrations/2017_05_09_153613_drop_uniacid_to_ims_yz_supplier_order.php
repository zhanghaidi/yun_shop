<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropUniacidToImsYzSupplierOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('yz_supplier_order', function (Blueprint $table) {
            if (Schema::hasColumn('ims_yz_supplier_order', 'uniacid')) {
                $table->dropColumn('uniacid');
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
        Schema::table('yz_supplier_order', function (Blueprint $table) {
            $table->integer('uniacid')->nullable()->after('deleted_at');
        });
    }
}
