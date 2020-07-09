<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLngAndLatToYzSupplier extends Migration
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
                if (!Schema::hasColumn('yz_supplier', 'lng')) {
                    $table->string('lng', 20)->nullable()->default('')->comment('经度');
                }

                if (!Schema::hasColumn('yz_supplier', 'lat')) {
                    $table->string('lat', 20)->nullable()->default('')->comment('纬度');
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
            $table->dropColumn('lng');
            $table->dropColumn('lat');
        });
    }
}
