<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateYzPluginJdSupplyOrderOrderSnColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_plugin_jd_supply_order')) {
            if (Schema::hasColumn('yz_plugin_jd_supply_order', 'order_sn')) {
                Schema::table('yz_plugin_jd_supply_order', function ($table) {
                    $table->string('order_sn')->nullable()->default('')->change();
                });
            }

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
