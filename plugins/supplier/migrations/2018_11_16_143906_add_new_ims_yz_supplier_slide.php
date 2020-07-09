<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewImsYzSupplierSlide extends Migration
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
            Schema::table('yz_supplier_slide', function (Blueprint $table) {
                    if (!Schema::hasColumn('yz_supplier_slide', 'created_at'))
                    {
                        $table->integer('created_at')->nullable();
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
    }
}
