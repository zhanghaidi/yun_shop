<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAddressToYzSupplierTable extends Migration
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

                if (!Schema::hasColumn('yz_supplier', 'province_id')) {
                    $table->integer('province_id')->default(0)->comment('省级id');
                }

                if (!Schema::hasColumn('yz_supplier', 'city_id')) {
                    $table->integer('city_id')->default(0)->comment('市级id');
                }

                if (!Schema::hasColumn('yz_supplier', 'district_id')) {
                    $table->integer('district_id')->default(0)->comment('区级id');
                }

                if (!Schema::hasColumn('yz_supplier', 'street_id')) {
                    $table->integer('street_id')->default(0)->comment('街道id');
                }

                if (!Schema::hasColumn('yz_supplier', 'street_name')) {
                    $table->string('street_name', 50)->nullable()->default('')->comment('街道名称');
                }

                if (!Schema::hasColumn('yz_supplier', 'address')) {
                    $table->string('address')->nullable()->default('')->comment('详细地址');
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
            $table->dropColumn('province_id');
            $table->dropColumn('city_id');
            $table->dropColumn('district_id');
            $table->dropColumn('street_id');
            $table->dropColumn('street_name');
            $table->dropColumn('address');
        });
    }
}
