<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeOrderGoodsInformationToImsYzSupplierOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (\Schema::hasTable('yz_supplier_order')) {
            Schema::table('yz_supplier_order', function (Blueprint $table) {
                if (Schema::hasColumn('yz_supplier_order', 'order_goods_information')) {
                    $table->text('order_goods_information')->change();
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
        Schema::table('yz_supplier_order', function (Blueprint $table) {
            if (Schema::hasColumn('yz_supplier_order', 'order_goods_information')) {
                $table->string('order_goods_information')->change();
            }
        });
    }
}