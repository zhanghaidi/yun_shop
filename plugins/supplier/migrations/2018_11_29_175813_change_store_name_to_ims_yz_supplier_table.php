<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeStoreNameToImsYzSupplierTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*if (\Schema::hasTable('yz_supplier')) {
            \Schema::table('yz_supplier', function (Blueprint $table) {
                if (\Schema::hasColumn('yz_supplier', 'store_name')) {
                    $table->string('store_name', 100)->nullable()->change();
                    \Yunshop\Supplier\common\models\Supplier::select(['store_name', 'id'])->where('store_name', 'null')->update(['store_name' => NULL]);
                }
            });
        }*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}