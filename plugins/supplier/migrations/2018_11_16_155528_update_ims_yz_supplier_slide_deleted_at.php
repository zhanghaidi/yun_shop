<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateImsYzSupplierSlideDeletedAt extends Migration
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
                if (Schema::hasColumn('yz_supplier_slide', 'deleted_at'))
                {
                    $table->integer('deleted_at')->nullable()->default(null)->change();
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
