<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDiyformDataIdToImsYzSupplierTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_supplier')) {
            Schema::table('yz_supplier', function (Blueprint $table) {
                if (!Schema::hasColumn('yz_supplier', 'diyform_data_id')) {
                    $table->integer('diyform_data_id')->default(0);
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
        Schema::table('yz_supplier', function (Blueprint $table) {
            $table->dropColumn('diyform_data_id');
        });
    }
}
