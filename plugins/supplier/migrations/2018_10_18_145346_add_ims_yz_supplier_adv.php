<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImsYzSupplierAdv extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (Schema::hasTable('yz_supplier_adv')) {
            Schema::table('yz_supplier_adv',
                function (Blueprint $table) {
                    if (!Schema::hasColumn('yz_supplier_adv', 'supplier_uid'))
                    {
                        $table->integer('supplier_uid')->nullable()->default(0)->comment('供应商辅助ID');
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
        //
        Schema::table('yz_supplier_adv', function (Blueprint $table) {
            $table->dropColumn('supplier_uid');
        });
    }
}
