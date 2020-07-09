<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRegionAddressToYzSupplierTable extends Migration
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

                if (!Schema::hasColumn('yz_supplier', 'province_name')) {
                    $table->string('province_name', 60)->nullable()->default('')->comment('省级名称');
                }

                if (!Schema::hasColumn('yz_supplier', 'city_name')) {
                    $table->string('city_name', 60)->nullable()->default('')->comment('市级名称');
                }

                if (!Schema::hasColumn('yz_supplier', 'district_name')) {
                    $table->string('district_name', 60)->nullable()->default('')->comment('区级名称');
                }

                if (!Schema::hasColumn('yz_supplier', 'grade')) {
                    $table->tinyInteger('grade')->nullable()->default(0)->comment('0：无效；1：省级；2：市级；3：区级');
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
            $table->dropColumn('province_name');
            $table->dropColumn('city_name');
            $table->dropColumn('district_name');
            $table->dropColumn('grade');
        });
    }
}
