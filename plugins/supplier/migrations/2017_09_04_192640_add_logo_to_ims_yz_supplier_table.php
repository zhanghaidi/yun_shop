<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLogoToImsYzSupplierTable extends Migration
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
                if (!Schema::hasColumn('yz_supplier', 'logo')) {
                    $table->string('logo', 255)->default('');
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
            $table->dropColumn('logo');
        });
    }
}
