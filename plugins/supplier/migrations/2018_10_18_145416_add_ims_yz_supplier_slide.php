<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImsYzSupplierSlide extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (Schema::hasTable('yz_supplier_slide')) {
            Schema::table('yz_supplier_slide',
                function (Blueprint $table) {
                    if (!Schema::hasColumn('yz_supplier_slide', 'supplier_uid'))
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
        Schema::table('yz_supplier_slide', function (Blueprint $table) {
            $table->dropColumn('supplier_uid');
        });
    }
}
